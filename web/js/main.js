var socketServer = Clank.connect(_CLANK_URI);
var socketSession = null;
var clientID = null;

socketServer.on("socket/connect", function (session) {
    $.notify("Успешное подключение к сокету");

    socketSession = session;

    socketSession.call("ticket/get_client_id", {})
        .then(
            function (result) {
                clientID = result.clientID;
            }
        );

    getReservation();

    socketSession.subscribe("handler/channel", function (uri, payload) {
        var method = payload.event ? payload.event.method || '' : '';
        var ids = payload.event ? payload.event.ids : [];
        var status = payload.event ? payload.event.status : false;
        var CID = payload.event ? payload.event.clientID : false;

        switch (method) {
            case 'set_reservation':
            case 'update_reservation':
                getReservation();
                break;
            case 'set_booked':
                ids.forEach(function (id, i, arr) {
                    var cartItem = $('tr[data-id="' + id + '"]');
                    var ticketItem = $('.place[data-id="' + id + '"]');

                    if (getCartIds().indexOf(id) > 0) {
                        cartItem.remove();
                    }

                    cartItem.remove();
                    ticketItem.addClass('booked');
                    if (CID !== clientID) {
                        checkBooked();
                    }
                    ticketItem.removeClass('reservation').removeClass('reservation-my');

                    updateTicketsStatistic();
                });
                break;
        }
    });

    init();
});

socketServer.on("socket/disconnect", function (session) {
    $.notify({message: "Ошибка подключение к сокету"}, {type: "danger"});
});

window.onbeforeunload = function (e) {
    socketSession.unsubscribe("handler/channel");
};


$(document).ready(function () {
    $('#by-btn').click(function () {
        var ids = getCartIds();
        if (ids.length > 0) {
            setBooked(getCartIds(), function () {
                $.notify('Билеты зарезервированы!');
            })
        } else {
            $.notify('Выберите билеты');
        }
    })
});

function init() {
    $('#ticket-table .place').click(function () {
        var ticket = $(this);

        if (ticket.hasClass('booked')) {
            return;
        }

        var cart = $('#cart tbody');
        var cartItem = $('tr[data-id="' + ticket.data('id') + '"]');
        var ticketItem = $('.place[data-id="' + ticket.data('id') + '"]');

        if (cartItem.length > 0) {
            setReservation([ticket.data('id')], false, function (result) {
                cartItem.remove();
                ticketItem.removeClass('reservation-my');
            });
        } else {
            setReservation([ticket.data('id')], true, function (result) {
                cart.append('<tr data-id="' + ticket.data('id') + '"><td>' + ticket.data('sector') + '</td><td>' + ticket.data('series') + '</td><td>' + ticket.text() + '</td></tr>');
                ticketItem.addClass('reservation-my');
            });
        }
    });
}

function setReservation(ids, status, callback) {
    socketSession.call("ticket/set_reservation", {ids: ids, status: status})
        .then(
            function (result) {
                socketSession.publish("handler/channel", {
                    method: "set_reservation",
                    ids: ids,
                    status: status
                });
                callback(result);
            },
            function (error, desc) {
                $.notify(error);
                callback(error, desc);
            }
        );
}

function setBooked(ids, callback) {
    socketSession.call("ticket/set_booked", {ids: ids})
        .then(
            function (result) {
                socketSession.publish("handler/channel", {
                    method: "set_booked",
                    ids: ids,
                    clientID: result.clientID,
                    freeTickets: result.freeTickets,
                    freeTicketsForSector: result.freeTicketsForSector
                });
                clearCart();
                callback(result);
            },
            function (error, desc) {
                $.notify(error);
                callback(error, desc);
            }
        );
}

function getCartIds() {
    var ids = [];
    $('#cart tr:not(.cart-template):not(:first)').each(function () {
        ids.push($(this).data('id'));
    });

    return ids;
}

function clearCart() {
    $('#cart tr:not(.cart-template):not(:first)').remove();
}

function getReservation() {
    $('.place').removeClass('reservation').removeClass('reservation-my');

    socketSession.call("ticket/get_reservation", {})
        .then(
            function (result) {
                Object.keys(result).forEach(function (key, index) {
                    result[key].forEach(function (id, i, arr) {
                        var ticketItem = $('.place[data-id="' + id + '"]');
                        if (key == clientID) {
                            ticketItem.addClass('reservation-my');
                        } else {
                            ticketItem.addClass('reservation');
                        }
                    });
                });
            }
        );
}

function checkBooked() {
    var reservationReservedPlace = $('.place.reservation-my.booked');
    if (reservationReservedPlace.length > 0) {
        var place = $(reservationReservedPlace).text();
        var sector = $(reservationReservedPlace).data('sector');
        var series = $(reservationReservedPlace).data('series');

        var reservedPlaceMessage = 'Место:' + place + ' ряд:' + series + ' сектор:' + sector;

        $.notify("Следующее мето уже зарезервировано: \r\n" + reservedPlaceMessage);
    }
}

function updateTicketsStatistic() {
    socketSession.call("ticket/get_tickets_statistics", {})
        .then(
            function (result) {
                $('.total-free-places').text(result.freeTickets);
                $(result.freeTicketsForSector).each(function () {
                    $('.sector-' + this.sector).find(' .sector-free-places').text(this.total);
                });
            }
        );
}