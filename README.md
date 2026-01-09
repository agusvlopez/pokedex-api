# 📱 Pokedex API

Una aplicación web interactiva para explorar Pokémon, construida con **Laravel 12** y el **PokeAPI**. Permite consultar información detallada de Pokémon.

---

## 🚀 Instalación y Ejecución

### Requisitos Previos

- **PHP**: ^8.2
- **Composer**: v2.0 o superior
- **Node.js**: v18 o superior (para npm)
- **Laravel Sail** (recomendado): Entorno Docker predeterminado

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/agusvlopez/pokedex-api.git
   cd pokedex-api
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Instalar dependencias de Node.js**
   ```bash
   npm install
   ```

### Ejecución

En una terminal:
```bash
php artisan serve
```

En otra terminal:
```bash
npm run dev
```

La aplicación estará disponible en `http://localhost:8000`
---

## 📁 Organización del Código

### Estructura del Proyecto

```
app/
├── Http/
│   └── Controllers/
│       └── PokemonController.php      # Controlador principal
├── Services/
│   └── PokemonService.php             # Lógica de negocio
└── Exceptions/
    └── NotFoundException.php          # Excepción personalizada

routes/
├── web.php                            # Rutas HTTP

resources/
├── views/
│   ├── pokemons/
│   │   ├── index.blade.php           # Lista de Pokémon
│   │   ├── show.blade.php            # Detalle de Pokémon
│   │   └── search.blade.php          # Resultados de búsqueda
│   ├── layouts/
│   │ └── app.blade.php               # Layout principal
│   └── components/                   # Componentes reutilizables
│       └── alerts.blade.php          # alertas personalizadas 
└── css/
    └── app.css                       # Estilos con Tailwind CSS
    
```

### Patrones de Diseño Utilizados

1. **Service Layer Pattern**: La clase `PokemonService` encapsula toda la lógica de negocio relacionada con Pokémon, separando esto del controlador.

2. **Dependency Injection**: El `PokemonController` inyecta el `PokemonService` en el constructor para facilitar testing y desacoplamiento.

3. **Repository Pattern (implícito)**: El servicio actúa como una capa de abstracción entre la API externa y los controladores.

4. **Exception Handling**: Uso de excepciones personalizadas (`NotFoundException`) para errores específicos del dominio.

---

## ⚡ Funcionalidad Adicional Implementada

### 1. **Caché Inteligente (Cache Layer)**
   - **Implementación**: Sistema de caché con expiración configurable
   - **Estrategia de búsqueda**:
     - `getPokemonIndex(500)`: Cachea un índice de los primeros 500 pokémon por 24h. Primera llamada descarga de API, las siguientes usan caché local.
     - `getPokemons()`: Cachea listados paginados por 1 hora para el listado principal.
     - `getPokemon()`: Cachea detalles individuales (nombre, stats, tipos, etc.) por 1 hora.
   - **Ventaja**: Búsquedas parciales sin llamadas adicionales a API. Una sola petición masiva inicial, luego filtrado local instantáneo.
   - **Refresh manual**: `getPokemonIndex(500, true)` fuerza descarga fresca si es necesario.

### 2. **Búsqueda Inteligente de Pokémon**
   - **Coincidencia exacta**: Primero intenta encontrar el Pokémon exacto por nombre o ID (vía `getPokemon()`).
   - **Búsqueda parcial**: Si no hay coincidencia exacta, busca en el índice cacheado (primeros 500 pokémon).
   - **Filtrado local**: Realiza la búsqueda en memoria sin nuevas llamadas a API (muy rápido).
   - **Límites controlados**: 
     - `$limit = 20` → resultados a devolver.
     - `$searchLimit = 500` → rango de pokémon indexados (configurable).
   - **Validación de entrada**: Requiere mínimo 2 caracteres, máximo 50.
   - **Mensajes personalizados**: Errores en español con contexto claro.

### 3. **Manejo de Errores**
   - Excepciones personalizadas para casos específicos (implementado uno a modo de ejemplo, la idea es implementarlo con los distintos tipos de errores, en especial los más comunes, como 401, 500, etc.)
   - Logging detallado de errores en `storage/logs/`
   - Respuestas HTTP apropiadas (404 cuando no se encuentra Pokémon)
   - Fallback graceful: Retorna lista vacía si hay error en lugar de fallar

### 4. **Optimización de Imágenes**
   - Utiliza sprites de alta definición desde GitHub (PokeAPI sprites)
   - Dos versiones de imagen: `image` y `image_hd` para flexibilidad
   - Fallback automático si no hay imagen oficial

---

## 🔧 Decisiones Técnicas

### 1. **Sistema de Caché (Cache Facades)**
   **Por qué**:
   - Evita exceder límites de rate limiting de PokeAPI
   - Mejora significativamente el tiempo de respuesta

### 2. **MVC + Service Layer**
   **Por qué**:
   - Separa la lógica de negocio del controlador (manejandola en el servicio)
   - Facilita testing unitario
   - Código más mantenible y escalable
   - Permite reutilizar servicios en múltiples controladores

### 3. **Tailwind CSS + Vite**
   **Por qué**:
   - Tailwind: rápido de desarrollar
   - Vite: Bundler moderno y rápido. Ideal en proyectos pequeños como este.

### 4. **Validación**

**Por qué**:
- Valida que `query` cumpla los requisitos antes de ejecutar la lógica.
- Mejora la estabilidad y seguridad: evita excepciones y cualquier dato que podrían romper el flujo.
- Mejora la experiencia del frontend: devuelve mensajes legibles para el usuario.

Nota: en proyectos grandes es preferible usar Form Requests (por ejemplo en `app/Http/Requests`) en lugar de validar directamente en el controlador. Acá se utiliza `$request->validate()` por simplicidad.

Ejemplo:
```php
public function search(Request $request)
{
   $data = $request->validate([
      'query' => 'required|string|min:2|max:50',
   ], [
      'query.required' => 'Por favor ingresá un nombre',
      'query.min' => 'Ingresá al menos 2 caracteres',
   ]);
}
```

---

## 📊 Flujo de Datos

```
Usuario → Navegador → Laravel Router → PokemonController 
         ↓
    Valida Entrada
         ↓
    PokemonService
         ↓
    Verifica Caché
         ├─ Caché válida → Retorna datos
         └─ Caché expirada → Consulta PokeAPI → Cachea resultado
         ↓
    View (Blade Template)
         ↓
    HTML + CSS/JS → Usuario
```

---

## 🔗 Recursos

- [Laravel](https://laravel.com/docs/12.x)
- [PokeAPI](https://pokeapi.co/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Vite](https://vitejs.dev/)

---

**Versión**: 1.0.0  
**Última actualización**: Enero 2026
