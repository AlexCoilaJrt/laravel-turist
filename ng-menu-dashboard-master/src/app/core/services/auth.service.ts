import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, switchMap, tap } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private API_BASE         = 'http://127.0.0.1:8000/api/v1/auth';
  private LOGIN_URL        = `${this.API_BASE}/login`;
  private REGISTER_URL     = `${this.API_BASE}/register`;
  private REFRESH_URL      = `${this.API_BASE}/refresh`;
  private LOGOUT_URL       = `${this.API_BASE}/logout`;
  private ME_URL           = `${this.API_BASE}/me`;
  private CSRF_URL         = 'http://127.0.0.1:8000/sanctum/csrf-cookie';

  private tokenKey         = 'authToken';
  private refreshTokenKey  = 'refreshToken';

  constructor(private http: HttpClient, private router: Router) {}

  register(userData: {
    name: string;
    email: string;
    password: string;
    rol: string;
  }): Observable<any> {
    return this.http.get(this.CSRF_URL, { withCredentials: true }).pipe(
      switchMap(() =>
        this.http.post<any>(this.REGISTER_URL, userData, { withCredentials: true }).pipe(
          tap(() => this.router.navigate(['/login']))
        )
      )
    );
  }

  login(email: string, password: string): Observable<any> {
    return this.http
      .post<any>(this.LOGIN_URL, { email, password }, { withCredentials: true })
      .pipe(
        tap(response => {
          if (response.access_token) {
            this.setToken(response.access_token);

            if (response.refresh_token) {
              this.setRefreshToken(response.refresh_token);
              this.autoRefreshToken();
            }

            // ✅ Redirige al dashboard si todo fue bien
            this.router.navigate(['/dashboard']);
          }
        })
      );
  }

  logout(): void {
    this.http.post(this.LOGOUT_URL, {}, { withCredentials: true })
      .subscribe(() => {
        this.clearAuthData();
        this.router.navigate(['/']);
      });
  }

  getMe(): Observable<any> {
    return this.http.get(this.ME_URL, { withCredentials: true });
  }

  private refreshToken(): Observable<any> {
    const refreshToken = this.getRefreshToken();
    return this.http
      .post<any>(this.REFRESH_URL, { refresh_token: refreshToken }, { withCredentials: true })
      .pipe(
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
      const exp     = payload.exp * 1000;
      const delay   = exp - Date.now() - 60_000;

      if (delay > 0) {
        setTimeout(() => this.refreshToken().subscribe(), delay);
      }
    } catch (err) {
      console.error('Error parsing token payload:', err);
    }
  }

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
      return Date.now() < payload.exp * 1000;  // Verifica si el token no ha expirado
    } catch {
      return false;
    }
  }
  
}
