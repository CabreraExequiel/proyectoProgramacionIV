// archivo: src/app/services/canchas.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Cancha {
  id?: number; // opcional para nuevas canchas
  nombre: string;
  tipo: string;
  precio_hora?: number;
  cant_jugadores?: number;
}

@Injectable({
  providedIn: 'root'
})
export class CanchasService {

  // ⚡ URL base de tu API Laravel
  private apiUrl = 'http://127.0.0.1:8000/api/canchas2';

  constructor(private http: HttpClient) { }

  // Listar todas las canchas
  getCanchas(): Observable<Cancha[]> {
    return this.http.get<Cancha[]>(this.apiUrl);
  }

  // Obtener una cancha por id
  getCancha(id: number): Observable<Cancha> {
    return this.http.get<Cancha>(`${this.apiUrl}/${id}`);
  }

  // Crear una nueva cancha
  crearCancha(cancha: Cancha): Observable<Cancha> {
    return this.http.post<Cancha>(this.apiUrl, cancha);
  }

  // Actualizar una cancha existente
  actualizarCancha(id: number, cancha: Cancha): Observable<Cancha> {
    return this.http.put<Cancha>(`${this.apiUrl}/${id}`, cancha);
  }

  // Eliminar una cancha
  eliminarCancha(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
