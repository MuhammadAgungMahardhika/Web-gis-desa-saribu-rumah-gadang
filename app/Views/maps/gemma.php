<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with AI</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>Chat with AI</h1>

    <form id="chatForm">
        <input type="text" id="inquiry" name="inquiry" placeholder="Ask something..." required>
        <button type="submit">Ask</button>
    </form>

    <h2>AI Response:</h2>
    <div id="response"></div>

    <script>
        $(document).ready(function() {
            $("#chatForm").submit(function(e) {
                e.preventDefault();
                const inquiry = $("#inquiry").val();

                $.ajax({
                    url: "<?= site_url('web/gemma/ask') ?>",
                    method: "POST",
                    data: {
                        inquiry: inquiry
                    },
                    success: function(response) {
                        console.log(response)
                        $('#response').text(response.answer);
                    },
                    error: function() {
                        $('#response').text('Sorry, something went wrong.');
                    }
                });
            });
        });
    </script>
</body>

</html>