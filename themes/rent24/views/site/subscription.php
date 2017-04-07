<div id="subscription-dialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close">×</a>
                <h3>Подписка на рассылку</h3>
            </div>
            <div class="modal-body">
                <div class="modal-form-wrapper">
                    <p>Укажите свои данные:</p>
                    <label>ФИО:</label>
                    <input type="text" name="name" value="" style="width:400px" />
                    <label>Email:</label>
                    <input type="text" name="email" value="" />
                    <label>Телефон:</label>
                    <input type="text" name="phone" value="" />
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" id="btnConfirm" class="btn confirm" onclick="apartmentSubscribe()">Подписаться</a>
                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn secondary">Отмена</a>
            </div>
        </div>
    </div>
</div>

<div id="message-dialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close">×</a>
                <h3>Сообщение</h3>
            </div>
            <div class="modal-body">
                <p id="modal-message"></p>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" id="btnConfirm" class="btn confirm" onclick="apartmentMessageCallback()">Ok</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function apartmentSubscribe() {
        var name = $('#subscription-dialog input[name=name]').val();
        var email = $('#subscription-dialog input[name=email]').val();
        var phone = $('#subscription-dialog input[name=phone]').val();

        if (!name.length) {
            apartmentMessageCallback.callback = apartmentDialog;
            apartmentMessage('Укажите ваше имя');
            return;
        }

        if (!email.length && !phone.length) {
            apartmentMessageCallback.callback = apartmentDialog;
            apartmentMessage('Укажите ваш email или телефон');
            return;
        }

        $.post(
            '/apartments/main/subscribe',
            {
                'name': name,
                'email': email,
                'phone': phone
            },
            function(response) {
                if (response) {
                    if (response.error) {
                        apartmentMessageCallback.callback = apartmentDialog;
                        apartmentMessage(response.error);
                    } else if (response.message) {
                        apartmentMessageCallback.callback = null;
                        apartmentMessage(response.message);
                    }
                }
            },
            'json'
        );
    }

    function apartmentDialog() {
        $('#subscription-dialog').modal('show');
    }

    function apartmentMessage(message) {
        $('#message-dialog #modal-message').text(message);
        $('#message-dialog').modal('show');
    }

    apartmentMessageCallback = function() {
        if (typeof(apartmentMessageCallback.callback)=='undefined' || !apartmentMessageCallback.callback) return;
        apartmentMessageCallback.callback.call();
    }
</script>