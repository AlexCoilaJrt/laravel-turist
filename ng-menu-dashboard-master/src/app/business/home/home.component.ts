import { Component } from '@angular/core';
import { CommonModule } from '@angular/common'; // Necesario para directivas como *ngIf/*ngFor
import { RouterModule } from '@angular/router'; // Necesario para routerLink

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export default class HomeComponent { // <-- Cambia esto
  // Lógica del componente
}