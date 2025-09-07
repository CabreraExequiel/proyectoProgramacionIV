import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ReservaService {
  getReservasActivas() {
    throw new Error('Method not implemented.');
  }
  private apiUrl = 'http://127.0.0.1:8000/api'; 

  constructor(private http: HttpClient) {}

  getCanchas(): Observable<any> {
    return this.http.get(`${this.apiUrl}/canchas2`);
  }
  getReservas(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/reservas`);
  }
  getHorarios(fecha: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/horarios?fecha=${fecha}`);
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

    getReservasUsuario(): Observable<any> {
    return this.http.get(`${this.apiUrl}/reservations`);
    }
}
















// import { Injectable } from '@angular/core';
// import { HttpClient, HttpHeaders } from '@angular/common/http';
// import { Observable } from 'rxjs';

// @Injectable({
//   providedIn: 'root'
// })
// export class ReservaService {
//   private apiUrl = 'http://localhost:8000/api/reservas'; 

//   constructor(private http: HttpClient) {}

//   getCanchas(): Observable<any> {
//     return this.http.get(`${this.apiUrl}/canchas`);
//   }

//   getHorarios(fecha: string): Observable<any> {
//     return this.http.get(`${this.apiUrl}/horarios?fecha=${fecha}`);
//   }

//   crearReserva(reserva: any): Observable<any> {
//     return this.http.post(`${this.apiUrl}/reservas`, reserva);
//   }

//     getReservasUsuario(): Observable<any> {
//     return this.http.get(`${this.apiUrl}/reservations`);
//     }
// }
