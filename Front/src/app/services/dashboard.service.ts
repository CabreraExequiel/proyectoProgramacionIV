// src/app/services/dashboard.service.ts

import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DashboardService {
  private apiUrl = 'http://127.0.0.1:8000/api'; 
  private tokenKey = 'access_token';

  constructor(private http: HttpClient) {}

  private getAuthHeaders(): HttpHeaders {
    const token = localStorage.getItem(this.tokenKey);
    if (token) {
      return new HttpHeaders({
        'Authorization': `Bearer ${token}`
      });
    }
    return new HttpHeaders();
  }

  getMetrics(): Observable<{ ocupacion: number; reservas_activas: number }> {
    return this.http.get<{ ocupacion: number; reservas_activas: number }>(
      `${this.apiUrl}/reservas/metrics`,
      { headers: this.getAuthHeaders() }
    );
  }

  getReservasActivas(): Observable<any[]> {
    return this.http.get<any[]>(
      `${this.apiUrl}/reservas/activas`,
      { headers: this.getAuthHeaders() }
    );
  }

  getUsuariosRegistrados(): Observable<any[]> {
    return this.http.get<any[]>(
      `${this.apiUrl}/usuarios-registrados`,
      { headers: this.getAuthHeaders() }
    );
  }

  getReservasPendientes(): Observable<any[]> {
    return this.http.get<any[]>(
      `${this.apiUrl}/reservas/pendientes`,
      { headers: this.getAuthHeaders() }
    );
  }

  actualizarEstadoReserva(id: number, nuevoEstado: string): Observable<any> {
    return this.http.put(
      `${this.apiUrl}/reservas/${id}/estado`, 
      { estado: nuevoEstado },
      { headers: this.getAuthHeaders() }
    );
  }

  getIngresosMensuales(): Observable<{ ingresos: number }> {
    return this.http.get<{ ingresos: number }>(
      `${this.apiUrl}/reservas/ingresos`,
      { headers: this.getAuthHeaders() }
    );
  }

  getAll(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/canchas2`);
  }
}
