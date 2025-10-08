import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ReservaService {
  getReservasActivas(): Observable<any[]> {
  return this.http.get<any[]>(`${this.apiUrl}/reservas/activas`);
}

  getReservasUsuario(userId: number): Observable<any> {
  return this.http.get(`${this.apiUrl}/reservations?user_id=${userId}`);
}

  private apiUrl = 'http://127.0.0.1:8000/api'; 

  constructor(private http: HttpClient) {}

  getCanchas(): Observable<any> {
    return this.http.get(`${this.apiUrl}/canchas2`);
  }
  getReservas(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/reservas`);
  }
getHorarios(fecha: string, canchaId: number) {
  return this.http.get<string[]>(`${this.apiUrl}/horarios`, {
    params: { fecha, canchaId }
  });
}


crearReserva(reserva: any): Observable<any> {
  const token = localStorage.getItem('access_token'); 
  if (!token) {
    // Retornamos un observable que emite error
    return new Observable(observer => {
      observer.error({ message: 'Usuario no autenticado' });
    });
  }

  const headers = new HttpHeaders({
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  });

  return this.http.post(`${this.apiUrl}/reservas`, reserva, { headers });
}

}
