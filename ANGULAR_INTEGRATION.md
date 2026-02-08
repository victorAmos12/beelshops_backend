// Angular HTTP Client Configuration for BeelShops API

// ============================================
// 1. Environment Configuration
// ============================================

// environment.ts (Development)
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};

// environment.prod.ts (Production)
export const environment = {
  production: true,
  apiUrl: 'https://api.beelshops.com/api'
};

// ============================================
// 2. HTTP Interceptor
// ============================================

import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpErrorResponse
} from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable()
export class ApiInterceptor implements HttpInterceptor {
  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    // Ajouter le header Content-Type si absent
    if (!request.headers.has('Content-Type')) {
      request = request.clone({
        setHeaders: {
          'Content-Type': 'application/json'
        }
      });
    }

    // Ajouter le token d'authentification si disponible
    const token = localStorage.getItem('auth_token');
    if (token) {
      request = request.clone({
        setHeaders: {
          'Authorization': `Bearer ${token}`
        }
      });
    }

    return next.handle(request).pipe(
      catchError((error: HttpErrorResponse) => {
        console.error('API Error:', error);
        return throwError(() => error);
      })
    );
  }
}

// ============================================
// 3. Services Examples
// ============================================

// produit.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ProduitService {
  private apiUrl = `${environment.apiUrl}/produits`;

  constructor(private http: HttpClient) { }

  // Récupérer tous les produits
  getProduits(page: number = 1, limit: number = 10, category?: number, search?: string): Observable<any> {
    let params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    if (category) {
      params = params.set('category', category.toString());
    }
    if (search) {
      params = params.set('search', search);
    }

    return this.http.get<any>(this.apiUrl, { params });
  }

  // Récupérer un produit par ID
  getProduit(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}`);
  }

  // Créer un produit
  createProduit(data: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, data);
  }

  // Mettre à jour un produit
  updateProduit(id: number, data: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${id}`, data);
  }

  // Supprimer un produit
  deleteProduit(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

  // Récupérer les produits par catégorie
  getProduitsByCategory(categoryId: number, page: number = 1, limit: number = 10): Observable<any> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    return this.http.get<any>(`${this.apiUrl}/categorie/${categoryId}`, { params });
  }
}

// panier.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class PanierService {
  private apiUrl = `${environment.apiUrl}/panier`;

  constructor(private http: HttpClient) { }

  // Récupérer le panier
  getPanier(userId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${userId}`);
  }

  // Ajouter un article au panier
  addArticle(userId: number, produitId: number, quantite: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${userId}/articles`, {
      produit_id: produitId,
      quantite: quantite
    });
  }

  // Mettre à jour la quantité
  updateArticle(articleId: number, quantite: number): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/articles/${articleId}`, {
      quantite: quantite
    });
  }

  // Supprimer un article
  removeArticle(articleId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/articles/${articleId}`);
  }

  // Vider le panier
  clearPanier(userId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${userId}`);
  }
}

// commande.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class CommandeService {
  private apiUrl = `${environment.apiUrl}/commandes`;

  constructor(private http: HttpClient) { }

  // Récupérer toutes les commandes
  getCommandes(page: number = 1, limit: number = 10, status?: string): Observable<any> {
    let params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    if (status) {
      params = params.set('status', status);
    }

    return this.http.get<any>(this.apiUrl, { params });
  }

  // Récupérer une commande
  getCommande(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}`);
  }

  // Créer une commande
  createCommande(data: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, data);
  }

  // Mettre à jour une commande
  updateCommande(id: number, data: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${id}`, data);
  }

  // Supprimer une commande
  deleteCommande(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

  // Récupérer les commandes d'un utilisateur
  getCommandesByUser(userId: number, page: number = 1, limit: number = 10): Observable<any> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    return this.http.get<any>(`${this.apiUrl}/utilisateur/${userId}`, { params });
  }
}

// avis.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AvisService {
  private apiUrl = `${environment.apiUrl}/avis`;

  constructor(private http: HttpClient) { }

  // Récupérer tous les avis
  getAvis(page: number = 1, limit: number = 10): Observable<any> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    return this.http.get<any>(this.apiUrl, { params });
  }

  // Créer un avis
  createAvis(data: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, data);
  }

  // Mettre à jour un avis
  updateAvis(id: number, data: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${id}`, data);
  }

  // Supprimer un avis
  deleteAvis(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

  // Récupérer les avis d'un produit
  getAvisByProduit(produitId: number, page: number = 1, limit: number = 10): Observable<any> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    return this.http.get<any>(`${this.apiUrl}/produit/${produitId}`, { params });
  }
}

// liste-souhaits.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ListeSouhaitsService {
  private apiUrl = `${environment.apiUrl}/liste-souhaits`;

  constructor(private http: HttpClient) { }

  // Récupérer la liste de souhaits
  getListeSouhaits(userId: number, page: number = 1, limit: number = 10): Observable<any> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());

    return this.http.get<any>(`${this.apiUrl}/${userId}`, { params });
  }

  // Ajouter à la liste de souhaits
  addToWishlist(userId: number, produitId: number): Observable<any> {
    return this.http.post<any>(this.apiUrl, {
      utilisateur_id: userId,
      produit_id: produitId
    });
  }

  // Supprimer de la liste de souhaits
  removeFromWishlist(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

  // Vérifier si un produit est dans la liste
  checkWishlist(userId: number, produitId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/check/${userId}/${produitId}`);
  }
}

// ============================================
// 4. App Module Configuration
// ============================================

import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { ApiInterceptor } from './interceptors/api.interceptor';

@NgModule({
  declarations: [
    // Components
  ],
  imports: [
    BrowserModule,
    HttpClientModule
  ],
  providers: [
    {
      provide: HTTP_INTERCEPTORS,
      useClass: ApiInterceptor,
      multi: true
    }
  ],
  bootstrap: [/* AppComponent */]
})
export class AppModule { }

// ============================================
// 5. Component Usage Examples
// ============================================

// produit-list.component.ts
import { Component, OnInit } from '@angular/core';
import { ProduitService } from '../services/produit.service';

@Component({
  selector: 'app-produit-list',
  templateUrl: './produit-list.component.html',
  styleUrls: ['./produit-list.component.css']
})
export class ProduitListComponent implements OnInit {
  produits: any[] = [];
  loading = false;
  error: string | null = null;
  page = 1;
  limit = 10;

  constructor(private produitService: ProduitService) { }

  ngOnInit(): void {
    this.loadProduits();
  }

  loadProduits(): void {
    this.loading = true;
    this.error = null;

    this.produitService.getProduits(this.page, this.limit).subscribe({
      next: (response) => {
        this.produits = response.data;
        this.loading = false;
      },
      error: (error) => {
        this.error = 'Erreur lors du chargement des produits';
        this.loading = false;
        console.error(error);
      }
    });
  }

  nextPage(): void {
    this.page++;
    this.loadProduits();
  }

  previousPage(): void {
    if (this.page > 1) {
      this.page--;
      this.loadProduits();
    }
  }
}

// panier.component.ts
import { Component, OnInit } from '@angular/core';
import { PanierService } from '../services/panier.service';

@Component({
  selector: 'app-panier',
  templateUrl: './panier.component.html',
  styleUrls: ['./panier.component.css']
})
export class PanierComponent implements OnInit {
  panier: any = null;
  loading = false;
  userId = 1; // À récupérer depuis l'authentification

  constructor(private panierService: PanierService) { }

  ngOnInit(): void {
    this.loadPanier();
  }

  loadPanier(): void {
    this.loading = true;
    this.panierService.getPanier(this.userId).subscribe({
      next: (response) => {
        this.panier = response.data;
        this.loading = false;
      },
      error: (error) => {
        console.error('Erreur:', error);
        this.loading = false;
      }
    });
  }

  updateQuantity(articleId: number, newQuantity: number): void {
    this.panierService.updateArticle(articleId, newQuantity).subscribe({
      next: () => {
        this.loadPanier();
      },
      error: (error) => {
        console.error('Erreur:', error);
      }
    });
  }

  removeArticle(articleId: number): void {
    this.panierService.removeArticle(articleId).subscribe({
      next: () => {
        this.loadPanier();
      },
      error: (error) => {
        console.error('Erreur:', error);
      }
    });
  }

  clearCart(): void {
    this.panierService.clearPanier(this.userId).subscribe({
      next: () => {
        this.loadPanier();
      },
      error: (error) => {
        console.error('Erreur:', error);
      }
    });
  }
}

// ============================================
// 6. Error Handling Service
// ============================================

import { Injectable } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class ErrorHandlerService {
  getErrorMessage(error: HttpErrorResponse): string {
    if (error.error instanceof ErrorEvent) {
      // Client-side error
      return `Erreur: ${error.error.message}`;
    } else {
      // Server-side error
      if (error.error && error.error.error) {
        return error.error.error;
      }
      return `Erreur serveur: ${error.status} ${error.statusText}`;
    }
  }
}
