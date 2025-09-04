import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DashboardService {
  private apiUrl = 'http://localhost:8000/api'; 

  constructor(private http: HttpClient) {}

  getMetrics(): Observable<{ ocupacion: number; reservas_activas: number }> {
    return this.http.get<{ ocupacion: number; reservas_activas: number }>(
      `${this.apiUrl}/dashboard-metrics`
    );
  }

  getReservasActivas(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/reservas-activas`);
  }

  getAll(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/canchas`);
  }
}
