# Auth Module
Handles authentication management for the E-Commerce API.

## Authentication Endpoints
- **POST /api/v1/login**: Authenticate and receive a Sanctum token.
- **POST /api/v1/logout**: Revoke the authenticated user's token (requires `auth:sanctum` middleware).

# User Module
Handles users management for the E-Commerce API.

## User Management Endpoints
- **GET /api/v1/users**: List all users (paginated).
- **POST /api/v1/users**: Create a new user.
- **GET /api/v1/users/{id}**: Show a user.
- **PUT /api/v1/users/{id}**: Update a user.
- **DELETE /api/v1/users/{id}**: Delete a user.

# Product Module
Handles product and category management for the E-Commerce API.

## Endpoints
### Products
- **GET /api/v1/products**: List all products (requires `view-products`).
  - Query Parameters:
    - `filter[id]`: Exact product ID (e.g., `filter[id]=1`).
    - `filter[name]`: Partial product name (e.g., `filter[name]=lap`).
    - `filter[sku]`: Partial SKU (e.g., `filter[sku]=LAP`).
    - `filter[price]`: Exact price (e.g., `filter[price]=999.99`).
    - `filter[price_lte]`: Price less than or equal (e.g., `filter[price_lte]=1000`).
    - `filter[price_gte]`: Price greater than or equal (e.g., `filter[price_gte]=500`).
    - `filter[stock]`: Exact stock (e.g., `filter[stock]=100`).
    - `filter[category_id]`: Category ID (e.g., `filter[category_id]=1`).
    - `sort`: Sort by field (e.g., `sort=price`, `sort=-price`).
    - `include`: Include categories (e.g., `include=categories`).
- **POST /api/v1/products**: Create a product (requires `manage-products`).
- **GET /api/v1/products/{id}**: Show a product (requires `manage-products`).
- **PUT /api/v1/products/{id}**: Update a product (requires `manage-products`).
- **DELETE /api/v1/products/{id}**: Delete a product (requires `manage-products`).

### Categories
- **GET /api/v1/categories**: List all categories (requires `view-categories`).
- Query Parameters:
    - `filter[id]`: Exact category ID (e.g., `filter[id]=1`).
    - `filter[name]`: Partial category name (e.g., `filter[name]=lap`).
- **POST /api/v1/categories**: Create a category (requires `manage-categories`).
- **GET /api/v1/categories/{id}**: Show a category (requires `manage-categories`).
- **PUT /api/v1/categories/{id}**: Update a category (requires `manage-categories`).
- **DELETE /api/v1/categories/{id}**: Delete a category (requires `manage-categories`).

# Order Module
Handles order management for the E-Commerce API.

## Endpoints
- **GET /api/v1/orders**: List all orders (requires `view-orders`).
- **POST /api/v1/orders**: Create an order (requires `manage-orders`).
- **GET /api/v1/orders/{id}**: Show an order (requires `manage-orders`).
- **PUT /api/v1/orders/{id}**: Update an order (requires `manage-orders`).
- **DELETE /api/v1/orders/{id}**: Delete an order (requires `manage-orders`).

# Notification Module
Handles notification management for the E-Commerce API.

## Endpoints
- **GET /api/v1/notifications**: List all notifications (requires `view-notifications`).
  - Query Parameters:
    - `filter[id]`: Exact notification ID (e.g., `filter[id]=1`).
    - `filter[user_id]`: User ID (e.g., `filter[user_id]=2`, admin-only).
    - `filter[type]`: Notification type (e.g., `filter[type]=order_status_updated`).
    - `filter[unread]`: Unread notifications (e.g., `filter[unread]=true`).
    - `sort`: Sort by field (e.g., `sort=created_at`, `sort=-read_at`).
    - `include`: Include user (e.g., `include=user`).
- **POST /api/v1/notifications**: Create a notification (requires `manage-notifications`).
- **GET /api/v1/notifications/{id}**: Show a notification (requires `manage-notifications` or ownership).
- **PUT /api/v1/notifications/{id}**: Update a notification (e.g., mark as read, requires `manage-notifications` or ownership).
- **DELETE /api/v1/notifications/{id}**: Delete a notification (requires `manage-notifications` or ownership).

## Events
- **OrderStatusUpdated**: Triggers a notification when an order’s status changes.
