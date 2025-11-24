# StellarGraphQL

GraphQL API powered by Lighthouse, featuring modular schema design, scalable resolvers, and production-ready Laravel integration.

## 📋 İçindekiler

- [Genel Bakış](#genel-bakış)
- [Teknoloji Stack](#teknoloji-stack)
- [Kurulum](#kurulum)
- [Yapılandırma](#yapılandırma)
- [API Dokümantasyonu](#api-dokümantasyonu)
- [Mimari](#mimari)
- [Kullanım Örnekleri](#kullanım-örnekleri)
- [Geliştirme](#geliştirme)
- [Test](#test)

## 🎯 Genel Bakış

StellarGraphQL, Laravel 12 ve Lighthouse GraphQL kullanılarak geliştirilmiş modern bir e-ticaret API'sidir. Proje, modüler mimari, ölçeklenebilir resolver'lar ve production-ready özellikler sunar.

### Özellikler

- ✅ **GraphQL API**: Query, Mutation ve Subscription desteği
- ✅ **Gelişmiş Arama**: Meilisearch entegrasyonu ile hızlı ürün arama
- ✅️ **Asenkron Checkout (Deneysel)**: Queue tabanlı checkout altyapısı mevcut ancak GraphQL şemasında henüz expose edilmedi ve job, `order_id` gibi ek veriler gerektiriyor
- ✅ **Gerçek Zamanlı Güncellemeler**: GraphQL Subscriptions ile sipariş durumu event'leri yayınlanır (event tetikleyici senaryoları sınırlı)
- ✅ **Ödeme Sistemi**: Factory Pattern ve Decorator Pattern ile esnek ödeme entegrasyonu
- ✅ **Kimlik Doğrulama**: Laravel Passport ile OAuth2 desteği
- ✅ **Pagination**: Hem normal hem de arama sonuçları için pagination desteği

## 🛠 Teknoloji Stack

- **Framework**: Laravel 12
- **GraphQL**: Lighthouse 6.63
- **Arama Motoru**: Meilisearch (Laravel Scout)
- **Kimlik Doğrulama**: Laravel Passport 13.4
- **Queue**: Laravel Queue (async işlemler)
- **Subscriptions**: GraphQL Subscriptions (Pusher/Echo)
- **PHP**: 8.4+

## 📦 Kurulum

### Gereksinimler

- PHP 8.2 veya üzeri
- Composer
- Node.js ve NPM
- SQLite/MySQL/PostgreSQL
- Meilisearch (opsiyonel, arama özelliği için)

### Adımlar

1. **Projeyi klonlayın**
   ```bash
   git clone <repository-url>
   cd StellarGraphQL
   ```

2. **Bağımlılıkları yükleyin**
   ```bash
   composer install
   npm install
   ```

3. **Ortam değişkenlerini yapılandırın**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Veritabanını yapılandırın**
   `.env` dosyasında veritabanı ayarlarınızı yapın:
   ```env
   DB_CONNECTION=postgres
   ...
   ```

5. **Veritabanını oluşturun ve migrate edin**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed
   ```

6. **Meilisearch'i yapılandırın (opsiyonel)**
   ```env
   SCOUT_DRIVER=meilisearch
   MEILISEARCH_HOST=http://127.0.0.1:7700
   MEILISEARCH_KEY=your-master-key
   ```

   Meilisearch'i başlatın:
   ```bash
   # Docker ile
   docker run -d -p 7700:7700 getmeili/meilisearch:latest
   ```

   Ürünleri indexleyin:
   ```bash
   php artisan scout:import "App\Models\Product"
   ```

7. **Laravel Passport'u kurun (kimlik doğrulama için)**
   ```bash
   php artisan passport:install
   ```

8. **Queue worker'ı başlatın**
   ```bash
   php artisan queue:work
   ```

9. **Sunucuyu başlatın**
   ```bash
   php artisan serve
   ```

   GraphQL endpoint: `http://localhost:8000/graphql`

## ⚙️ Yapılandırma

### GraphQL Endpoint

GraphQL API varsayılan olarak `/graphql` endpoint'inde çalışır. Bu ayar `config/lighthouse.php` dosyasında değiştirilebilir.

### Cache Temizleme

Schema veya query cache'ini temizlemek için:

```bash
php artisan lighthouse:clear-cache
php artisan optimize:clear
php artisan config:clear
```

### Queue Yapılandırması

Queue driver'ını `.env` dosyasında yapılandırın:

```env
QUEUE_CONNECTION=redis
```

### Broadcasting Yapılandırması

GraphQL Subscriptions için broadcasting yapılandırması:

```env
BROADCAST_DRIVER=pusher
# veya
BROADCAST_DRIVER=redis
LIGHTHOUSE_BROADCASTER=pusher
LIGHTHOUSE_SUBSCRIPTION_STORAGE=redis
```

## 📚 API Dokümantasyonu

### Queries

#### Ürün Arama (Meilisearch)

Meilisearch kullanarak gelişmiş ürün araması:

```graphql
query {
  searchProducts(
    query: "laptop"
    brand: "Apple"
    minPrice: 1000
    maxPrice: 5000
    inStock: true
    page: 1
    perPage: 20
  ) {
    data {
      id
      title
      price
      stock
      brand
    }
    current_page
    last_page
    total
  }
}
```

#### Ürün Listeleme (Normal Pagination)

Veritabanından normal pagination ile ürün listeleme:

```graphql
query {
  products(page: 1, perPage: 20) {
    data {
      id
      title
      price
      stock
      brand
    }
    paginatorInfo {
      currentPage
      lastPage
      total
      hasMorePages
    }
  }
}
```

#### Kullanıcı Arama

```graphql
query {
  searchUsers(
    query: "john"
    email: "john@example.com"
    page: 1
    perPage: 20
  ) {
    data {
      id
      name
      email
      username
    }
    current_page
    last_page
    total
  }
}
```

#### Kullanıcı Listeleme

```graphql
query {
  users(page: 1, perPage: 20) {
    data {
      id
      name
      email
      is_active
    }
    paginatorInfo {
      currentPage
      lastPage
      total
    }
  }
}
```

#### Adres Arama

```graphql
query {
  searchAddresses(
    query: "Istanbul"
    city: "Istanbul"
    country: "Turkey"
    page: 1
    perPage: 20
  ) {
    data {
      id
      user_id
      street
      city
      country
      postal_code
    }
    current_page
    last_page
    total
  }
}
```

### Mutations

#### Ürün Satın Alma

```graphql
mutation {
  buyProduct(
    productId: "1"
    quantity: 2
    paymentMethodId: "1"
  ) {
    success
    message
    orderId
  }
}
```

**Response:**
```json
{
  "data": {
    "buyProduct": {
      "success": true,
      "message": "Purchase successful",
      "orderId": "123"
    }
  }
}
```

### Subscriptions

#### Sipariş Durumu Güncellemeleri

Gerçek zamanlı sipariş durumu takibi:

```graphql
subscription {
  orderStatusUpdated(orderId: "123") {
    orderId
    status
    message
    updatedAt
  }
}
```

**Event Yayınlama:**

Sipariş durumu değiştiğinde `OrderStatusUpdated` event'i otomatik olarak yayınlanır:

```php
event(new OrderStatusUpdated($order, 'Payment successful'));
```

## 🏗 Mimari

### Proje Yapısı

```
app/
├── Events/              # Event sınıfları
│   └── OrderStatusUpdated.php
├── GraphQL/
│   ├── Mutations/       # GraphQL Mutations
│   │   ├── CheckoutMutation.php
│   │   └── PurchaseMutation.php
│   └── Queries/         # GraphQL Queries
│       ├── AddressQuery.php
│       ├── ProductQuery.php
│       └── UserQuery.php
├── Jobs/                # Queue Jobs
│   └── ProcessCheckoutJob.php
├── Models/              # Eloquent Models
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Payment.php
│   ├── Product.php
│   ├── User.php
│   └── UserAddress.php
└── Services/
    └── Payment/         # Ödeme servisleri
        ├── Decorators/
        │   └── PaymentLoggerDecorator.php
        ├── Providers/
        │   └── DefaultPaymentProvider.php
        ├── PaymentProviderFactory.php
        └── PaymentProviderInterface.php
```

### Veritabanı Şeması

#### Orders (Siparişler)

- `id`: Primary key
- `uuid`: Unique identifier
- `user_id`: Kullanıcı ID (foreign key)
- `address_id`: Adres ID (foreign key)
- `total_amount`: Toplam tutar
- `status`: Durum (pending, paid, failed, canceled)
- `payment_transaction_id`: Ödeme işlem ID'si
- `timestamps`

#### Order Items (Sipariş Kalemleri)

- `id`: Primary key
- `order_id`: Sipariş ID (foreign key)
- `product_id`: Ürün ID (foreign key)
- `product_name`: Ürün adı (snapshot)
- `unit_price`: Birim fiyat (snapshot)
- `quantity`: Miktar
- `total_price`: Toplam fiyat
- `timestamps`

#### Products (Ürünler)

- `id`: Primary key
- `uuid`: Unique identifier
- `title`: Ürün adı
- `slug`: URL slug
- `sku`: SKU kodu
- `brand`: Marka
- `price`: Fiyat
- `stock`: Stok miktarı
- `is_active`: Aktif durumu
- `timestamps`

#### Payments (Ödemeler)

- `id`: Primary key
- `order_id`: Sipariş ID (foreign key)
- `provider`: Ödeme sağlayıcısı (stripe, paypal, etc.)
- `method`: Ödeme yöntemi
- `amount`: Tutar
- `currency`: Para birimi
- `transaction_id`: İşlem ID'si
- `status`: Durum (pending, paid, failed, refunded, canceled)
- `payload`: Raw response (JSON)
- `timestamps`

### Design Patterns

#### 1. Factory Pattern (Ödeme Sağlayıcıları)

Ödeme sağlayıcılarını oluşturmak için Factory Pattern kullanılır:

```php
$provider = PaymentProviderFactory::create('stripe');
$result = $provider->pay(100.00, ['order_id' => 123]);
```

#### 2. Decorator Pattern (Ödeme Loglama)

Ödeme işlemlerini loglamak için Decorator Pattern kullanılır:

```php
// PaymentLoggerDecorator otomatik olarak tüm ödemeleri loglar
$provider = new PaymentLoggerDecorator($baseProvider);
```

#### 3. Queue Pattern (Asenkron İşlemler - Deneysel)

- `ProcessCheckoutJob` sınıfı tanımlı ancak GraphQL şemasında aktif bir checkout mutation'ı bulunmuyor.
- Job, `order_id` dahil ayrıntılı bir payload bekliyor; mevcut `CheckoutMutation` bunu sağlamadığı için job tek başına çalıştırılırsa hata alabilir.
- Üretim öncesi, mutation'ın şemaya eklenmesi, payload'ın genişletilmesi ve job'ın gerekli order kayıtlarını oluşturacak şekilde güncellenmesi gerekiyor.

Örnek (eksik alanları vurgulamak için):

```php
ProcessCheckoutJob::dispatch([
    'order_id' => $order->id, // Şu an mutation tarafından gönderilmiyor
    'user_id' => $order->user_id,
    'provider' => 'mock',
]);
```

## 💡 Kullanım Örnekleri

### 1. Ürün Arama ve Filtreleme

```graphql
query SearchLaptops {
  searchProducts(
    query: "laptop"
    brand: "Apple"
    minPrice: 1000
    maxPrice: 5000
    inStock: true
    page: 1
    perPage: 10
  ) {
    data {
      id
      title
      price
      stock
      brand
      sku
    }
    current_page
    last_page
    total
  }
}
```

### 2. Ürün Satın Alma ve Durum Takibi

**Adım 1: Ürün satın al**

```graphql
mutation PurchaseProduct {
  buyProduct(
    productId: "1"
    quantity: 2
    paymentMethodId: "1"
  ) {
    success
    message
    orderId
  }
}
```

**Adım 2: Sipariş durumunu dinle**

```graphql
subscription WatchOrder {
  orderStatusUpdated(orderId: "123") {
    orderId
    status
    message
    updatedAt
  }
}
```

### 3. Kimlik Doğrulama ile Sorgu

```bash
# Token ile istek
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "{ users { id name email } }"
  }'
```

## 🔧 Geliştirme

### Geliştirme Ortamı

Proje, geliştirme için hazır script'ler içerir:

```bash
# Tüm servisleri başlat (server, queue, logs, vite)
composer run dev
```

Bu komut şunları başlatır:
- Laravel development server
- Queue worker
- Laravel Pail (log viewer)
- Vite dev server

### Kod Standartları

Proje Laravel Pint kullanır:

```bash
# Kod formatlama
./vendor/bin/pint
```

### Schema Güncelleme

Schema'yı güncelledikten sonra cache'i temizleyin:

```bash
php artisan lighthouse:clear-cache
php artisan optimize:clear
```

### Yeni Query Ekleme

1. `graphql/schema.graphql` dosyasına query tanımı ekleyin
2. `app/GraphQL/Queries/` klasörüne resolver sınıfı oluşturun
3. Schema'da `@field` directive'i ile resolver'ı bağlayın

Örnek:

```graphql
# schema.graphql
type Query {
  myQuery: String!
    @field(resolver: "App\\GraphQL\\Queries\\MyQuery@resolve")
}
```

```php
// app/GraphQL/Queries/MyQuery.php
namespace App\GraphQL\Queries;

class MyQuery
{
    public function resolve($root, array $args)
    {
        return "Hello World";
    }
}
```

### Yeni Mutation Ekleme

1. `graphql/schema.graphql` dosyasına mutation tanımı ekleyin
2. `app/GraphQL/Mutations/` klasörüne mutation sınıfı oluşturun
3. Schema'da `@field` directive'i ile bağlayın

### Yeni Ödeme Sağlayıcısı Ekleme

1. `PaymentProviderInterface` implement eden yeni bir provider oluşturun:

```php
namespace App\Services\Payment\Providers;

use App\Services\Payment\PaymentProviderInterface;

class StripePaymentProvider implements PaymentProviderInterface
{
    public function pay(float $amount, array $metadata = []): array
    {
        // Stripe API çağrısı
        return [
            'status' => 'success',
            'transaction_id' => 'stripe_xxx',
            'amount' => $amount,
        ];
    }
}
```

2. `PaymentProviderFactory`'ye ekleyin:

```php
case 'stripe':
    $instance = new StripePaymentProvider();
    break;
```

## 🧪 Test

### Test Çalıştırma

```bash
# Tüm testler
composer run test

# veya
php artisan test
```

### Test Yapısı

```
tests/
├── Feature/     # Feature testleri
└── Unit/         # Unit testleri
```

## 📝 Notlar

### Sipariş Durumları

- `pending`: Sipariş oluşturuldu ama ödeme alınmadı
- `paid`: Ödeme başarılı
- `failed`: Ödeme başarısız
- `canceled`: Kullanıcı veya sistem tarafından iptal edildi

### Ödeme Durumları

- `pending`: Ödeme bekleniyor
- `paid`: Ödeme tamamlandı
- `failed`: Ödeme başarısız
- `refunded`: İade edildi
- `canceled`: İptal edildi

### Cache Stratejisi

- **Schema Cache**: Production'da otomatik aktif
- **Query Cache**: Varsayılan olarak aktif (24 saat TTL)
- **Validation Cache**: Varsayılan olarak kapalı

### Güvenlik

- GraphQL introspection production'da kapatılabilir
- Query complexity ve depth limitleri yapılandırılabilir
- Laravel Passport ile OAuth2 kimlik doğrulama

## 🚧 Bilinen Eksikler ve Yapılacaklar

- `CheckoutMutation` sınıfı mevcut olsa da `graphql/schema.graphql` içinde expose edilmedi; bu nedenle asenkron checkout akışı çalıştırılamıyor.
- `ProcessCheckoutJob` içinde `PaymentProviderFactory::make()` çağrısı yapılıyor fakat factory'de yalnızca `create()` metodu bulunuyor; bu uyumsuzluk giderilmeli.
- Aynı job, `Order` modelinde var olmayan `total_price` ve `transaction_id` alanlarını kullanıyor; doğru alanlar `total_amount` ve `payment_transaction_id`.
- `PurchaseMutation`, `orders` tablosunda yer almayan kolonlara (`product_id`, `quantity`, `total_price`, `payment_method_id`) veri yazmaya çalışıyor; bu bilgiler `order_items` tablosuna taşınmalı.
- GraphQL subscription'ı tetikleyen event yalnızca `ProcessCheckoutJob` içinde yer alıyor; job devreye alınmadığı için subscription pratikte tetiklenmiyor.

## 🚀 Production Deployment

### Önerilen Ayarlar

1. **Environment Variables**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   LIGHTHOUSE_SCHEMA_CACHE_ENABLE=true
   LIGHTHOUSE_QUERY_CACHE_ENABLE=true
   ```

2. **Queue Worker**
   ```bash
   php artisan queue:work --daemon
   
   php artisan queue:worker-payment
   
   ```

3. **Cache Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Schema Cache**
   ```bash
   php artisan lighthouse:cache
   ```

## 📄 Lisans

YOK