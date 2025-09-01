import { Routes } from '@angular/router';
import { CategoriasComponent } from './componentes/categorias/categorias.component';
import { LandingComponent } from './landing/landing.component'; 

export const routes: Routes = [
  { path: '', component: LandingComponent },          
  { path: 'categorias', component: CategoriasComponent }, 
  { path: '**', redirectTo: '', pathMatch: 'full' } 
];
