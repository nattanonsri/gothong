<script>
    function checkPassword(text) {
        if ($(`#${text}`).attr('type') == 'text') {
            $(`#${text}`).attr('type', 'password');
            $(`.${text}`).html('<i class="fa-regular fa-eye-slash"></i>');
        } else {
            $(`#${text}`).attr('type', 'text');
            $(`.${text}`).html('<i class="fa-regular fa-eye"></i>');
        }

    }

    function notifySocketIO(endpoint, data) {
        try {
            $.ajax({
                url: `<?= getenv('SOCKET_IO_URL') ?>/api/${endpoint}`,
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    console.log('Socket notification sent successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to send socket notification:', error);
                }
            });
        } catch (error) {
            console.error('Error sending socket notification:', error);
        }
    }
</script>