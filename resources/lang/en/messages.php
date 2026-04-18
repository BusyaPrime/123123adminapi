<?php

    return [
        'Вы начали загрузку?' => 'Have you started loading?',
        'Вы закончили загрузку?' => 'Have you finished loading?',
        'Доступен новый заказ' => 'New order available',
        'Заказ' => 'Order #:arg',
        'Новое уведомление' => 'New notification',
        'push_messages' => [
            'car_moderated' => 'Congratulations! You have successfully passed moderation. Now you can take orders!',
            'order' => 'Order #:arg',
            'new_order' => 'Order',
            'status_changed' => 'Status of the order was changed to: :arg',
            'status_confirmation' => 'Have you received your goods? Please confirm. If you don\'t, system will take the order as completed automatically in :arg hours',
            'booking_auto_confirmed' => 'Due to the lack of confirmation of the order status from your side, the system has transferred order #:arg as completed. Thank you for your cooperation!',
            'client_confirmed_driver_offer' => 'Client have received your offer, please click here to see order details',
            'another_truck' => 'You cannot accept this order because this order does not fit your vehicle',
            'booking_accepted' => 'Sorry, booking was accepted by another driver',
            'truck_already_have_orders' => 'You cannot bid because you have 2 active orders',
            'new_driver_offer' => 'New driver offer',
            'order_already_accepted' => 'Sorry, you cannot edit booking price because it\'s accepted',
            'equal_to_zero' => 'Error saving the offer',
            
            'client_accepted_another_driver' => 'К сожалению, ваше предложение не подошло клиенту. Клиент выбрал другого водителя',

            'article' => 'News',
            'article_text' => 'New articles in Casva',

            'message' => 'Technical Support',
            'message_text' => 'You have new message from Technical Support',

            'booking_message' => 'New message',
            'booking_text' => 'New message from order',
            
            'confirmation_accepted' => 'Your request to complete the order has been accepted by client',
            'confirmation_rejected' => 'Your request to complete the order has been rejected by client',

            'price_changed' => 'An additional payment in the amount of :sum is added to the amount of your order. Reason: :argument',
            'new_order_available_edited' => 'New order was edited and pushed back to market',
            'published_with_new_price' => 'Price of this order was edited, click here to see it',
            'have_you_started_loading' => 'Have you started loading ?',
            'have_you_ended_loading' => 'Have you ended loading ?',
            'offers_limit_exceeded' => 'You can offer maximum 3 prices for each order',
        ],
        'booking_statuses' => [
            'free' => 'Free',
            'in_progress' => 'In progress',
            'new' => 'New',
            'waiting' => 'Waiting driver',
            'accepted' => 'Driver accepted',
            'assigned' => 'Driver assigned',
            'arrived' => 'Loading',
            'canceled' => 'Canceled',
            'processing' => 'On the way',
            'pause' => 'Unloading',
            'done' => 'Delvered',
            'order' => 'New',
            'confirmation' => 'Waiting for confirmation',
        ],
    ];


?>