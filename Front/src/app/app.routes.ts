import { Routes } from '@angular/router';
import { CategoriasComponent } from './componentes/categorias/categorias.component';

export const routes: Routes = [
  { path: 'categorias', component: CategoriasComponent }, // /categorias
  { path: '', redirectTo: 'categorias', pathMatch: 'full' } // redirige a /categorias
];