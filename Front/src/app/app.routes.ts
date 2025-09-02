import { Routes } from '@angular/router';
import { CategoriasComponent } from './componentes/categorias/categorias.component';
import { LoginComponent } from './componentes/login/login.component';
import { LandingComponent } from './landing/landing.component';

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: '', component: LandingComponent },
  { path: 'categorias', component: CategoriasComponent },
  { path: '**', redirectTo: '', pathMatch: 'full' } //Unifiqué rutas de Login y Landing, eliminando marcadores de conflicto

];
