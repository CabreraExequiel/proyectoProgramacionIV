import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root' // Hace que el servicio esté disponible en toda la app
})
export class CategoriasService {
  private apiUrl = 'http://127.0.0.1:8000/api/categorias'; // Ajusta según tu backend

  constructor(private http: HttpClient) {}

  getAll(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  create(categoria: any): Observable<any> {
    return this.http.post(this.apiUrl, categoria);
  }
}
