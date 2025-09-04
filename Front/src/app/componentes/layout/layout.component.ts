import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
// import { SidebarComponent } from '../sidebar/sidebar.component'; 
// import { NavbarComponent } from '../navbar/navbar.component';  

@Component({
  selector: 'app-layout',
  standalone: true,
  imports: [RouterModule],
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.css']
})
export class LayoutComponent {}
