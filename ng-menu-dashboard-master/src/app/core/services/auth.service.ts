import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, tap, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private API_BASE = 'http://127.0.0.1:8000/api/v1/auth';
  private LOGIN_URL = `${this.API_BASE}/login`;
  private REGISTER_URL = `${this.API_BASE}/register`;
  private REFRESH_URL = `${this.API_BASE}/refresh`;
  private LOGOUT_URL = `${this.API_BASE}/logout`;
  private ME_URL = `${this.API_BASE}/me`;
  private CSRF_URL = 'http://127.0.0.1:8000/sanctum/csrf-cookie';

  private tokenKey = 'authToken';
  private refreshTokenKey = 'refreshToken';

  constructor(private http: HttpClient, private router: Router) {}

  register(userData: any): Observable<any> {
    return this.http.get(this.CSRF_URL).pipe(
      switchMap(() => {
        return this.http.post(this.REGISTER_URL, userData).pipe(
          tap(() => this.router.navigate(['/login']))
        );
      })
    );
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post<any>(this.LOGIN_URL, { email, password }).pipe(
      tap(response => {
        if (response.access_token) {
          this.setToken(response.access_token);
          if (response.refresh_token) {
            this.setRefreshToken(response.refresh_token);
            this.autoRefreshToken();
          }
        }
      })
    );
  }

  logout(): void {
    this.http.post(this.LOGOUT_URL, {}).subscribe(() => {
      this.clearAuthData();
      this.router.navigate(['/login']);
    });
  }

  getMe(): Observable<any> {
    return this.http.get(this.ME_URL);
  }

  private refreshToken(): Observable<any> {
    const refreshToken = this.getRefreshToken();
    return this.http.post<any>(this.REFRESH_URL, { refresh_token: refreshToken }).pipe(
      tap(response => {
        if (response.access_token) {
          this.setToken(response.access_token);
        }
      })
    );
  }

  private autoRefreshToken(): void {
    const token = this.getToken();
    if (!token) return;

    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      const exp = payload.exp * 1000;
      const timeout = exp - Date.now() - (60 * 1000); // 1 min antes

      if (timeout > 0) {
        setTimeout(() => {
          this.refreshToken().subscribe();
        }, timeout);
      }
    } catch (err) {
      console.error('Error parsing token payload:', err);
    }
  }

  // Helpers con validación de entorno navegador
  private setToken(token: string): void {
    if (typeof window !== 'undefined') {
      localStorage.setItem(this.tokenKey, token);
    }
  }

  private getToken(): string | null {
    if (typeof window !== 'undefined') {
      return localStorage.getItem(this.tokenKey);
    }
    return null;
  }

  private setRefreshToken(token: string): void {
    if (typeof window !== 'undefined') {
      localStorage.setItem(this.refreshTokenKey, token);
    }
  }

  private getRefreshToken(): string | null {
    if (typeof window !== 'undefined') {
      return localStorage.getItem(this.refreshTokenKey);
    }
    return null;
  }

  private clearAuthData(): void {
    if (typeof window !== 'undefined') {
      localStorage.removeItem(this.tokenKey);
      localStorage.removeItem(this.refreshTokenKey);
    }
  }

  isAuthenticated(): boolean {
    const token = this.getToken();
    if (!token) return false;

    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      return Date.now() < payload.exp * 1000;
    } catch {
      return false;
    }
  }
}
