import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ReservaService {
  private apiUrl = 'http://localhost:8000/api/reservas'; 

  constructor(private http: HttpClient) {}

  getCanchas(): Observable<any> {
    return this.http.get(`${this.apiUrl}/canchas`);
  }

  getHorarios(fecha: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/horarios?fecha=${fecha}`);
  }

  crearReserva(reserva: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/reservas`, reserva);
  }

    getReservasUsuario(): Observable<any> {
    return this.http.get(`${this.apiUrl}/reservations`);
    }
}
