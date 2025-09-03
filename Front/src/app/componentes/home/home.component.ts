import { Component } from '@angular/core';
import { RouterModule, RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [RouterModule, RouterOutlet],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'] // <--- corregido
})
export class HomeComponent { }
