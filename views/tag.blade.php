<script>
    var brick = {
        sending: null,
        receiverUrl: '{{ $receiver }}',
        send: function (payload) {
            console.log('Sending payload', payload);
            brick.sending = payload;
        },
        check: function () {
            sending = brick.sending || {};
            brick.sending = null;
            return sending;
        },
        receiver: function (message, payload) {
            message = JSON.parse(message);
            payload = JSON.parse(payload);

            if (!message || !payload) {
                return {};
            }

            postData = {message: message, payload: payload, '_token': '{{ csrf_token() }}'};

            $.post('/brick/receiver', postData)
                .fail(function (xhr, status, error) {
                    if (xhr.statusText == 'abort') {
                        return;
                    }

                    alert('Something went wrong: ' + xhr.responseText);
                });

            return postData;
        }
    };

    @if ($message)
    brick.send({!! json_encode($message) !!});
    @endif

    $(function () {
        $(document).on('click', 'a[href^="http"]', function (e) {
            var url = $(this).attr('href');
            brick.send({
                'id': 'openBrowser',
                'url': url
            });
            e.preventDefault();
        });
    });
</script>
