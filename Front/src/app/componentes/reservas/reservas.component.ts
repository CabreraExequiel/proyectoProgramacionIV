import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ReservaService } from '../../services/reserva.service';
import { AuthService } from '../../services/auth.service';

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
  reservasActivas: any[] = [];
  reservasUsuario: any[] = [];
  horarios: string[] = [];
  horariosOcupados: string[] = [];
  horariosFin: string[] = [];
  mensaje: string = '';
  erroresBackend: { [key: string]: string[] } | null = null;
  esAdmin: boolean = false; //
  esMaster: boolean = false; 


  constructor(private fb: FormBuilder, private reservaService: ReservaService,  private authService: AuthService) {}

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

    this.reservaForm.get('hora_inicio')?.valueChanges.subscribe(() => {
      this.filtrarHorariosFin();
    });

    this.cargarCanchas();
    const usuario = this.authService.getUsuario();
    this.esAdmin = usuario?.role === 'administrador'; // 
    this.esMaster = usuario?.role === 'master';


  if (this.esAdmin || this.esMaster) {
    this.cargarReservas(); //
    this.cargarReservasActivas(); // 👈 acá
     } else if (usuario?.id) {
    this.cargarReservasUsuario(usuario.id);
  }
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

  cargarReservasActivas() {
  this.reservaService.getReservasActivas().subscribe({
    next: (data) => {
      this.reservasActivas = data;
    },
    error: (err) => {
      console.error('Error al cargar reservas activas', err);
    }
  });
}


  cargarReservasUsuario(userId: number) {
  this.reservaService.getReservasUsuario(userId).subscribe({
    next: (data) => {
      this.reservasUsuario = data;
    },
    error: (err) => console.error('Error al cargar reservas del usuario', err)
  });
}

  // Cargar horarios disponibles para la fecha seleccionada
cargarHorarios() {
  const fecha = this.reservaForm.get('fecha')?.value;
  const canchaId = this.reservaForm.get('cancha_id')?.value;

  if (!fecha || !canchaId) return;

  this.reservaService.getHorarios(fecha, canchaId).subscribe({
    next: (data) => {
      this.horarios = data;

      // Generar todos los horarios posibles
      const todosHorarios = [];
      for (let h = 16; h <= 23; h++) todosHorarios.push(`${h.toString().padStart(2,'0')}:00`);
      todosHorarios.push('00:00');

      // Determinar horarios ocupados
      this.horariosOcupados = todosHorarios.filter(h => !data.includes(h));

      // Actualizar opciones de hora_fin
      this.filtrarHorariosFin();
    },
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
  confirmarReserva() {
    if (this.reservaForm.invalid) return;

    this.reservaService.crearReserva(this.reservaForm.value).subscribe({
      next: (response) => {
        this.mensaje = response.message;
        this.toggleFormulario(); 
        this.cargarReservasActivas(); 
      },
      error: (err) => {
        if (err.status === 422) {
          this.erroresBackend = err.error.errors;
        } else if (err.status === 409) {
          // Conflicto de horario
          this.erroresBackend = { horario: ['Ya existe una reserva en ese horario'] };
        } else {
          this.erroresBackend = { general: [err.error?.error || 'Ocurrió un error'] };
        }
      }
    });
  }


filtrarHorariosFin() {
  const inicio = this.reservaForm.get('hora_inicio')?.value;
  if (!inicio) {
    this.horariosFin = this.horarios.filter(h => !this.horariosOcupados.includes(h));
    return;
  }

  let inicioHora = parseInt(inicio.split(':')[0], 10);
  let todasOpciones = [...this.horarios];
  if (!todasOpciones.includes('00:00')) todasOpciones.push('00:00');

  // Filtrar horas mayores a inicio y libres
  this.horariosFin = todasOpciones
    .filter(h => {
      let hora = parseInt(h.split(':')[0], 10);
      if (h === '00:00') hora = 24;
      return hora > inicioHora && !this.horariosOcupados.includes(h);
    })
    .sort((a, b) => {
      let horaA = a === '00:00' ? 24 : parseInt(a.split(':')[0], 10);
      let horaB = b === '00:00' ? 24 : parseInt(b.split(':')[0], 10);
      return horaA - horaB;
    });
}

}
