import { Routes } from '@angular/router';

import { LandingComponent } from './landing/landing.component'; 
import { HomeComponent } from './componentes/home/home.component';
import { InicioComponent } from './componentes/inicio/inicio.component';
import { LoginComponent } from './componentes/login/login.component';

export const routes: Routes = [
  { path: '', component: LandingComponent },   // página inicial
  { 
    path: 'home', 
    component: HomeComponent, 
    children:[
      { path: '', redirectTo: 'inicio', pathMatch: 'full' }, // redirige al inicio
      { path: 'inicio', component: InicioComponent }
    ] 
  }, 
  { path: 'login', component: LoginComponent },
  { path: '**', redirectTo: '', pathMatch: 'full' } 
];
