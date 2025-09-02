import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  standalone: true,
  imports:[ReactiveFormsModule,CommonModule],
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  loginForm: FormGroup;
  errorMessage: string = '';

  constructor(private fb: FormBuilder, private authService: AuthService) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

 onSubmit(): void {
  if (this.loginForm.invalid) return;

  this.authService.login(this.loginForm.value).subscribe({
    next: (res) => {
      if (res.access_token) {
        this.authService.saveToken(res.access_token);
        alert('Login exitoso!');
      }
    },
    error: (err) => {
      // Manejo seguro
      if (err?.error?.message) {
        this.errorMessage = err.error.message;
      } else if (err?.status === 0) {
        this.errorMessage = 'No se pudo conectar con el servidor';
      } else {
        this.errorMessage = 'Error en el login';
      }
    }
  });
 }
}
