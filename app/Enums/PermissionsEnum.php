<?php
namespace App\Enums;

enum PermissionsEnum: string {
    // User Management
    case VIEW_ANY_USER     = 'view any user';
    case VIEW_USER         = 'view user';
    case CREATE_USER       = 'create user';
    case UPDATE_USER       = 'update user';
    case DELETE_USER       = 'delete user';
    case RESTORE_USER      = 'restore user';
    case FORCE_DELETE_USER = 'force delete user';

    // Role Management
    case VIEW_ANY_ROLE     = 'view any role';
    case VIEW_ROLE         = 'view role';
    case CREATE_ROLE       = 'create role';
    case UPDATE_ROLE       = 'update role';
    case DELETE_ROLE       = 'delete role';
    case RESTORE_ROLE      = 'restore role';
    case FORCE_DELETE_ROLE = 'force delete role';

    // Category Management
    case VIEW_ANY_CATEGORY     = 'view any category';
    case VIEW_CATEGORY         = 'view category';
    case CREATE_CATEGORY       = 'create category';
    case UPDATE_CATEGORY       = 'update category';
    case DELETE_CATEGORY       = 'delete category';
    case RESTORE_CATEGORY      = 'restore category';
    case FORCE_DELETE_CATEGORY = 'force delete category';

    // Brand Management
    case VIEW_ANY_BRAND     = 'view any brand';
    case VIEW_BRAND         = 'view brand';
    case CREATE_BRAND       = 'create brand';
    case UPDATE_BRAND       = 'update brand';
    case DELETE_BRAND       = 'delete brand';
    case RESTORE_BRAND      = 'restore brand';
    case FORCE_DELETE_BRAND = 'force delete brand';

    // Product Management
    case VIEW_ANY_PRODUCT     = 'view any product';
    case VIEW_PRODUCT         = 'view product';
    case CREATE_PRODUCT       = 'create product';
    case UPDATE_PRODUCT       = 'update product';
    case DELETE_PRODUCT       = 'delete product';
    case RESTORE_PRODUCT      = 'restore product';
    case FORCE_DELETE_PRODUCT = 'force delete product';

    // Product Image Management
    case VIEW_ANY_PRODUCT_IMAGE     = 'view any product image';
    case VIEW_PRODUCT_IMAGE         = 'view product image';
    case CREATE_PRODUCT_IMAGE       = 'create product image';
    case UPDATE_PRODUCT_IMAGE       = 'update product image';
    case DELETE_PRODUCT_IMAGE       = 'delete product image';
    case RESTORE_PRODUCT_IMAGE      = 'restore product image';
    case FORCE_DELETE_PRODUCT_IMAGE = 'force delete product image';

    // Order Management
    case VIEW_ANY_ORDER     = 'view any order';
    case VIEW_ORDER         = 'view order';
    case CREATE_ORDER       = 'create order';
    case UPDATE_ORDER       = 'update order';
    case DELETE_ORDER       = 'delete order';
    case RESTORE_ORDER      = 'restore order';
    case FORCE_DELETE_ORDER = 'force delete order';

    // Order Item Management
    case VIEW_ANY_ORDER_ITEM     = 'view any order item';
    case VIEW_ORDER_ITEM         = 'view order item';
    case CREATE_ORDER_ITEM       = 'create order item';
    case UPDATE_ORDER_ITEM       = 'update order item';
    case DELETE_ORDER_ITEM       = 'delete order item';
    case RESTORE_ORDER_ITEM      = 'restore order item';
    case FORCE_DELETE_ORDER_ITEM = 'force delete order item';

    // Review Management
    case VIEW_ANY_REVIEW     = 'view any review';
    case VIEW_REVIEW         = 'view review';
    case CREATE_REVIEW       = 'create review';
    case UPDATE_REVIEW       = 'update review';
    case DELETE_REVIEW       = 'delete review';
    case RESTORE_REVIEW      = 'restore review';
    case FORCE_DELETE_REVIEW = 'force delete review';

    // Banner Management
    case VIEW_ANY_BANNER     = 'view any banner';
    case VIEW_BANNER         = 'view banner';
    case CREATE_BANNER       = 'create banner';
    case UPDATE_BANNER       = 'update banner';
    case DELETE_BANNER       = 'delete banner';
    case RESTORE_BANNER      = 'restore banner';
    case FORCE_DELETE_BANNER = 'force delete banner';

    // Cart Item Management
    case VIEW_ANY_CART_ITEM     = 'view any cart item';
    case VIEW_CART_ITEM         = 'view cart item';
    case CREATE_CART_ITEM       = 'create cart item';
    case UPDATE_CART_ITEM       = 'update cart item';
    case DELETE_CART_ITEM       = 'delete cart item';
    case RESTORE_CART_ITEM      = 'restore cart item';
    case FORCE_DELETE_CART_ITEM = 'force delete cart item';

    // Setting Management
    case VIEW_ANY_SETTING     = 'view any setting';
    case VIEW_SETTING         = 'view setting';
    case CREATE_SETTING       = 'create setting';
    case UPDATE_SETTING       = 'update setting';
    case DELETE_SETTING       = 'delete setting';
    case RESTORE_SETTING      = 'restore setting';
    case FORCE_DELETE_SETTING = 'force delete setting';

    // Wishlist Management
    case VIEW_ANY_WISHLIST_ITEM     = 'view any wishlist item';
    case VIEW_WISHLIST_ITEM         = 'view wishlist item';
    case CREATE_WISHLIST_ITEM       = 'create wishlist item';
    case UPDATE_WISHLIST_ITEM       = 'update wishlist item';
    case DELETE_WISHLIST_ITEM       = 'delete wishlist item';
    case RESTORE_WISHLIST_ITEM      = 'restore wishlist item';
    case FORCE_DELETE_WISHLIST_ITEM = 'force delete wishlist item';

    // Message Management
    case VIEW_ANY_MESSAGE_THREAD     = 'view any message thread';
    case VIEW_MESSAGE_THREAD         = 'view message thread';
    case REPLY_MESSAGE_THREAD        = 'reply message thread';
    case CLOSE_MESSAGE_THREAD        = 'close message thread';
    case REOPEN_MESSAGE_THREAD       = 'reopen message thread';

    // Notification Management
    case VIEW_ANY_NOTIFICATION     = 'view any notification';
    case VIEW_NOTIFICATION         = 'view notification';
    case MARK_AS_READ_NOTIFICATION = 'mark as read notification';
    case MARK_ALL_AS_READ_NOTIFICATION = 'mark all as read notification';

    // Customer Management
    case VIEW_ANY_CUSTOMER     = 'view any customer';
    case VIEW_CUSTOMER         = 'view customer';
    case CREATE_CUSTOMER       = 'create customer';
    case UPDATE_CUSTOMER       = 'update customer';
    case DELETE_CUSTOMER       = 'delete customer';
    case RESTORE_CUSTOMER      = 'restore customer';
    case FORCE_DELETE_CUSTOMER = 'force delete customer';

}
