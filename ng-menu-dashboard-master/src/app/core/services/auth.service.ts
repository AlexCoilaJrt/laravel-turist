// auth.service.ts
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, tap, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  // Endpoints de tu API Laravel
  private API_BASE = 'http://127.0.0.1:8000/api/v1/auth';
  private LOGIN_URL = `${this.API_BASE}/login`;
  private REGISTER_URL = `${this.API_BASE}/register`;
  private REFRESH_URL = `${this.API_BASE}/refresh`;
  private LOGOUT_URL = `${this.API_BASE}/logout`;
  private ME_URL = `${this.API_BASE}/me`;
  private CSRF_URL = 'http://127.0.0.1:8000/sanctum/csrf-cookie'; // CSRF para Sanctum

  // Keys para localStorage
  private tokenKey = 'authToken';
  private refreshTokenKey = 'refreshToken';

  constructor(private http: HttpClient, private router: Router) { }

  /**
   * Registra un nuevo usuario
   * @param userData {name, email, password, password_confirmation}
   */
  register(userData: any): Observable<any> {
    return this.http.get(this.CSRF_URL).pipe( // Primero obtiene el token CSRF
      switchMap(() => {
        return this.http.post(this.REGISTER_URL, userData).pipe(
          tap(() => {
            this.router.navigate(['/login']); // Redirige al login después del registro
          })
        );
      })
    );
  }

  /**
   * Inicia sesión
   * @param email 
   * @param password 
   */
  login(email: string, password: string): Observable<any> {
    return this.http.post<any>(this.LOGIN_URL, { email, password }).pipe(
      tap(response => {
        if (response.access_token) {
          this.setToken(response.access_token);
          if (response.refresh_token) {
            this.setRefreshToken(response.refresh_token);
            this.autoRefreshToken(); // Configura el auto-refresh
          }
        }
      })
    );
  }

  /**
   * Cierra la sesión
   */
  logout(): void {
    this.http.post(this.LOGOUT_URL, {}).subscribe(() => {
      this.clearAuthData();
      this.router.navigate(['/login']);
    });
  }

  /**
   * Obtiene los datos del usuario autenticado
   */
  getMe(): Observable<any> {
    return this.http.get(this.ME_URL);
  }

  /**
   * Refresca el token de acceso
   */
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

  /**
   * Configura el refresh automático del token
   */
  private autoRefreshToken(): void {
    const token = this.getToken();
    if (!token) return;

    const payload = JSON.parse(atob(token.split('.')[1]));
    const exp = payload.exp * 1000;
    const timeout = exp - Date.now() - (60 * 1000); // 1 minuto antes de expirar

    setTimeout(() => {
      this.refreshToken().subscribe();
    }, timeout);
  }

  // Helpers para el manejo de tokens
  private setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
  }

  private getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  private setRefreshToken(token: string): void {
    localStorage.setItem(this.refreshTokenKey, token);
  }

  private getRefreshToken(): string | null {
    return localStorage.getItem(this.refreshTokenKey);
  }

  private clearAuthData(): void {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.refreshTokenKey);
  }

  /**
   * Verifica si el usuario está autenticado
   */
  isAuthenticated(): boolean {
    const token = this.getToken();
    if (!token) return false;

    const payload = JSON.parse(atob(token.split('.')[1]));
    return Date.now() < payload.exp * 1000;
  }
}