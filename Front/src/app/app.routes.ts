import { Routes } from '@angular/router';
import { CategoriasComponent } from './componentes/categorias/categorias.component';
import { LoginComponent } from './componentes/login/login.component';



export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'categorias', component: CategoriasComponent },
  { path: '', redirectTo: 'login', pathMatch: 'full' } 
];
