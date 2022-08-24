{{-- Example: brickReceiver('{"id":"requestPushDeviceId"}', '{"name":"iPhone","deviceId":"b6682166-5070-4dc7-9ab3-05c20dd5c1e9"}') --}}

<script type="text/javascript">
    var brickReceiver = function (message, payload) {
        message = JSON.parse(message);
        payload = JSON.parse(payload);

        if (!message || !payload) {
            return {};
        }

        var requestedMessage = '{{ $message ? $message->id() : '' }}';
        if (requestedMessage === '' || message.id !== requestedMessage) {
            return;
        }

        postData = {message: message, payload: payload, '_token': '{{ csrf_token() }}'};

        fetch('/brick/receiver', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(postData),
        })
            .then(function (response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response;
            })
            .catch((error) => {
                if (error.name === "AbortError") {
                    return;
                }

                console.error('Something went wrong: ' + error);
            });

        return postData;
    };

    @if ($message)
    @if ($brickManager->isAndroid())
    brick.{{ $message->id() }}('{!! json_encode($message) !!}');
    @else
    if (typeof webkit !== "undefined") {
        webkit.messageHandlers.{{ $message->id() }}.postMessage({!! json_encode($message) !!});
    }
    @endif
    @endif

    document.addEventListener('click', e => {
        if (e.target.closest('.js-brick-file') && !e.bound) {
            e.bound = true;

            var element = e.target;

            element.removeAttribute('target');
            element.removeAttribute('rel');

            @if ($brickManager->isIos())
            e.preventDefault();

            var url = element.getAttribute('href');
            if (!/^[a-z][a-z0-9+.-]*:/.test(url)) {
                url = location.protocol + '//' + location.host + '/' + url.replace(/^\//g, '');
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw Error(response.statusText);
                    }
                    return response;
                })
                .then((response) => response.json())
                .then(function (data) {
                    console.log(data);
                    if (!data.url) {
                        throw Error('Unable to open file: URL unknown');
                    }

                    if (!data.filename) {
                        throw Error('Unable to open file: Filename unknown');
                    }

                    if (typeof webkit === "undefined") {
                        throw Error('Unable to open file: webkit bridge not setup');
                    }

                    webkit.messageHandlers.openFile.postMessage({
                        'id': 'openFile',
                        'filename': data.filename,
                        'url': data.url
                    });
                })
                .catch((error) => {
                    if (error.name === "AbortError") {
                        return;
                    }

                    console.error(error);
                    alert(error);
                });
            @endif
        }
    });

    @if ($brickManager->isIos())
    window.addEventListener('load', function () {
        var autofocus = document.querySelector('[autofocus]');
        if (autofocus) {
            window.scroll({
                top: autofocus.getBoundingClientRect().top + window.scrollY,
                behavior: 'smooth',
            });
        }
    }, false);
    @endif
</script>
