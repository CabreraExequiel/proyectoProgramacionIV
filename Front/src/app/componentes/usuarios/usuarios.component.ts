import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardService } from '../../services/dashboard.service';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-usuarios',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './usuarios.component.html',
  styleUrls: ['./usuarios.component.css']
})
export class UsuariosComponent implements OnInit {
  usuarios: any[] = [];
  cargando = true;
  error: string | null = null;
  usuario: any = null;
  esAdmin = false;

  constructor(
    private authService: AuthService,
    private router: Router,
    private dashboard: DashboardService
  ) {}

  ngOnInit(): void {
    // 🔹 Recuperar usuario autenticado
    this.usuario = this.authService.getUsuario();
    this.esAdmin = ['administrador', 'master'].includes(this.usuario?.role);

    // 🔹 Cargar usuarios solo si tiene permisos
    if (this.esAdmin) {
      this.cargarUsuarios();
    } else {
      this.cargando = false;
      this.error = 'No tiene permisos para ver esta sección.';
    }
  }

  // 🔹 Obtener usuarios registrados desde el backend
  cargarUsuarios(): void {
    this.cargando = true;
    this.dashboard.getUsuariosRegistrados().subscribe({
      next: (data) => {
        this.usuarios = data;
        this.cargando = false;
      },
      error: (err) => {
        console.error('Error al obtener usuarios:', err);
        this.error = 'No se pudieron cargar los usuarios registrados.';
        this.cargando = false;
      },
    });
  }

  // 🔹 Cerrar sesión
  cerrarSesion(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
