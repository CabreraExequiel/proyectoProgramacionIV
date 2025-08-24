import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { CategoriasService } from '../../services/categorias.service';

@Component({
  selector: 'app-categorias',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './categorias.component.html'
})
export class CategoriasComponent implements OnInit {
  categorias: any[] = [];
  nuevaCategoria: string = '';

  constructor(private categoriasService: CategoriasService) {}

  ngOnInit(): void {
    this.cargarCategorias();
  }

  cargarCategorias(): void {
    this.categoriasService.getAll().subscribe(data => {
      this.categorias = data;
    });
  }

  agregarCategoria(): void {
    if (this.nuevaCategoria.trim() === '') return;

    const categoria = { nombre: this.nuevaCategoria };

    this.categoriasService.create(categoria).subscribe(() => {
      this.nuevaCategoria = '';
      this.cargarCategorias();
    });
  }
}
