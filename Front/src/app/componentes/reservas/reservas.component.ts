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
  mostrarFormulario = false; 
  reservaForm!: FormGroup;
  canchas: any[] = [];
  reservas: any[] = []; 
  horarios: string[] = [];
  mensaje: string = '';
  erroresBackend: { [key: string]: string[] } | null = null;

  constructor(private fb: FormBuilder, private reservaService: ReservaService) {}

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
    this.cargarReservas(); // 👈 Carga inicial de reservas

  }

  // Obtiene el primer error de validación que viene del backend
  getPrimerError(key: string): string {
    if (!this.erroresBackend) return '';
    const val = this.erroresBackend[key];
    return Array.isArray(val) && val.length > 0 ? val[0] : '';
  }

  // Cargar canchas disponibles desde la API
  cargarCanchas() {
    this.reservaService.getCanchas().subscribe({
      next: (data) => this.canchas = data,
      error: (err) => console.error('Error al cargar canchas', err)
    });
  }

   cargarReservas() {
    this.reservaService.getReservas().subscribe({
      next: (data) => (this.reservas = data),
      error: (err) => console.error('Error cargando reservas:', err),
    });
  }
  // Cargar horarios disponibles para la fecha seleccionada
  cargarHorarios() {
    const fecha = this.reservaForm.get('fecha')?.value;
    if (!fecha) return;

    this.reservaService.getHorarios(fecha).subscribe({
      next: (data) => this.horarios = data,
      error: (err) => console.error('Error al cargar horarios', err)
    });
  }

  // Mostrar u ocultar el formulario
  toggleFormulario() {
    this.mostrarFormulario = !this.mostrarFormulario;

    // Si se cierra el formulario, limpiar datos
    if (!this.mostrarFormulario) {
      this.reservaForm.reset({ estado: 'activa' });
      this.horarios = [];
      this.erroresBackend = null;
    }
  }

  // Confirmar y enviar reserva al backend
  confirmarReserva() {
    if (this.reservaForm.invalid) return;

    this.reservaService.crearReserva(this.reservaForm.value).subscribe({
      next: (response) => {
        this.mensaje = response.message;
        this.toggleFormulario(); // Oculta el formulario después de crear la reserva
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
