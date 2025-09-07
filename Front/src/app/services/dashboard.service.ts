import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DashboardService {
  private apiUrl = 'http://127.0.0.1:8000/api'; 

  constructor(private http: HttpClient) {}

  getMetrics(): Observable<{ ocupacion: number; reservas_activas: number }> {
    return this.http.get<{ ocupacion: number; reservas_activas: number }>(
      `${this.apiUrl}/reservas/metrics`
    );
  }

  getReservasActivas(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/reservas/activas`);
  }


  getAll(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/canchas2`);
  }

  getUsuariosRegistrados(): Observable<any[]> {
  return this.http.get<any[]>(`${this.apiUrl}/usuarios-registrados`);
}
 getReservasPendientes(): Observable<any[]> {
  return this.http.get<any[]>(`${this.apiUrl}/reservas-pendientes`);
}

actualizarEstadoReserva(id: number, nuevoEstado: string): Observable<any> {
  return this.http.put(`${this.apiUrl}/reservas/${id}/estado`, { estado: nuevoEstado });
}
getIngresosMensuales(): Observable<{ ingresos: number }> {
  return this.http.get<{ ingresos: number }>(`${this.apiUrl}/reservas/ingresos`);
}

}
