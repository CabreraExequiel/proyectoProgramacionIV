import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ReservaService } from '../../services/reserva.service';

@Component({
  selector: 'app-reservas',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './reservas.component.html',
  styleUrls: ['./reservas.component.css']
})
export class ReservasComponent implements OnInit {
  reservaForm!: FormGroup;
  canchas: any[] = [];
  horarios: string[] = [];
  mensaje: string = '';
  erroresBackend: { [key: string]: string[] } | null = null;

  constructor(private fb: FormBuilder, private reservaService: ReservaService) {}

  getPrimerError(key: string): string {
    if (!this.erroresBackend) return '';
    const val = this.erroresBackend[key];
    if (Array.isArray(val) && val.length > 0) return val[0];
    return '';
  }

  ngOnInit(): void {
    this.reservaForm = this.fb.group({
      cliente: ['', Validators.required],
      cancha_id: ['', Validators.required],
      telefono: ['', Validators.required], 
      fecha: ['', Validators.required],
      hora_inicio: ['', Validators.required],
      hora_fin: ['', Validators.required],
      estado: ['activa', Validators.required] 
    });

    this.cargarCanchas();
  }

  cargarCanchas() {
    this.reservaService.getCanchas().subscribe({
      next: (data) => this.canchas = data,
      error: (err) => console.error('Error al cargar canchas', err)
    });
  }

  cargarHorarios() {
    const fecha = this.reservaForm.get('fecha')?.value;
    if (!fecha) return;

    this.reservaService.getHorarios(fecha).subscribe({
      next: (data) => this.horarios = data,
      error: (err) => console.error('Error al cargar horarios', err)
    });
  }

  confirmarReserva() {
    if (this.reservaForm.invalid) return;

    this.reservaService.crearReserva(this.reservaForm.value).subscribe({
      next: (response) => {
        this.mensaje = response.message;
        this.reservaForm.reset();
        this.horarios = [];
        this.erroresBackend = null;
      },
      error: (err) => {
        if (err.status === 422) {
          this.erroresBackend = err.error.errors;
        } else {
          console.error('Error al crear reserva', err);
        }
      }
    });
  }
}
