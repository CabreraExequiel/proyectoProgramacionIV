import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardService } from '../../services/dashboard.service';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  ocupacion: number | null = null;
  reservasActivas: number = 0;

  loading: boolean = true;
  error: boolean = false;

  selectedCard: string | null = null;
  reservasActivasList: any[] = [];

  constructor(private dashboardService: DashboardService) {}

  ngOnInit(): void {
    this.dashboardService.getMetrics().subscribe({
      next: (data) => {
        this.ocupacion = data.ocupacion;
        this.reservasActivas = data.reservas_activas;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error al cargar métricas:', err);
        this.error = true;
        this.loading = false;
      }
    });
  }

  mostrarDetalle(card: string) {
    this.selectedCard = card;

    if (card === 'reservasActivas') {
      this.cargarReservasActivas();
    }
  }

  cargarReservasActivas() {
    this.dashboardService.getReservasActivas().subscribe({
      next: (data) => {
        this.reservasActivasList = data;
      },
      error: (err) => {
        console.error('Error al cargar reservas activas:', err);
      }
    });
  }
}
