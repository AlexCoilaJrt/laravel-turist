// src/app/app.routes.ts
import { Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { AuthenticatedGuard } from './core/guards/authenticated.guard';

export const routes: Routes = [
  // Rutas públicas
  {
    path: '',
    children: [
      {
        // Ruta raíz: muestra el Home (landing)
        path: '',
        loadComponent: () =>
          import('./business/home/home.component').then(m => m.HomeComponent),
      },
      {
        // Ruta para login, accesible sólo si NO está autenticado
        path: 'login',
        loadComponent: () =>
          import('./business/authentication/login/login.component').then(m => m.LoginComponent),
        canActivate: [AuthenticatedGuard],
      },
      {
        path: 'register', // Nueva ruta de registro
        loadComponent: () =>
          import('./business/authentication/register/register.component').then(m => m.RegisterComponent),
        canActivate: [AuthenticatedGuard],
      },
    ],
  },
  // Rutas privadas dentro de un layout con sidebar u otros elementos comunes
  {
    path: '',
    loadComponent: () =>
      import('./shared/components/layout/layout.component').then(m => m.LayoutComponent),
    canActivate: [AuthGuard],
    children: [
      {
        path: 'dashboard',
        loadComponent: () =>
          import('./business/dashboard/dashboard.component').then(m => m.DashboardComponent),
      },
      {
        path: 'profile',
        loadComponent: () =>
          import('./business/profile/profile.component').then(m => m.ProfileComponent),
      },
      {
        path: 'tables',
        loadComponent: () =>
          import('./business/tables/tables.component').then(m => m.TablesComponent),
      },
      // Otras rutas privadas…
    ],
  },
  // Ruta comodín para redirección en caso de ruta no encontrada
  {
    path: '**',
    redirectTo: '',
  },
];
