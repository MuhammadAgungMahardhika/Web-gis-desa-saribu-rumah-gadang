<?= $this->extend('web/layouts/main'); ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container-fluid">
        <div class="card p-2 shadow-sm">
            <div class="card-header text-center card-title mb-2">Gemini AI</div>
            <div class="card-body">
                <div class="chat-box mb-3 p-2 border rounded bg-light" id="chat-box" style="height: 300px; overflow-y: auto;">
                    <p class="text-muted text-center">Percakapan akan muncul di sini...</p>
                </div>
                <div class="row d-flex">
                    <input type="text" id="input" class="form-control" placeholder="Ketik pesan...">
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button class="btn btn-danger" id="clear-chat">Hapus Riwayat</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadChatHistory();
    });

    document.querySelector("#input").addEventListener("keypress", async function(event) {
        if (event.key === "Enter") {
            let message = this.value.trim();
            if (!message) return;

            let chatBox = document.querySelector("#chat-box");
            appendMessage("user", message);
            this.value = "";

            let response = await fetch("<?= site_url('mobile/gemma/processRequest') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `message=${encodeURIComponent(message)}`
            });

            let result = await response.json();

            if (result.error) {
                appendMessage("error", "Error: " + result.error);
            } else {
                appendMessage("ai", result.response);
            }

            saveChatHistory();
        }
    });

    document.querySelector("#clear-chat").addEventListener("click", async function() {
        let chatBox = document.querySelector("#chat-box");
        chatBox.innerHTML = '<p class="text-muted text-center">Percakapan akan muncul di sini...</p>';

        await fetch("<?= site_url('mobile/gemma/resetChat') ?>", {
            method: "GET"
        });

        localStorage.removeItem("chatHistory");
    });

    function appendMessage(role, message) {
        let chatBox = document.querySelector("#chat-box");
        let messageElement = document.createElement("div");

        messageElement.classList.add("p-3", "rounded", "mb-2", "border", "shadow");

        // Set background putih
        messageElement.style.backgroundColor = "#ffffff";
        messageElement.style.color = "#000000"; // Teks hitam

        if (role === "user") {
            messageElement.classList.add("border-primary", "text-end");
        } else if (role === "ai") {
            messageElement.classList.add("border-secondary");
        } else {
            messageElement.classList.add("border-danger");
        }

        messageElement.textContent = message;

        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }


    function saveChatHistory() {
        let chatBox = document.querySelector("#chat-box");
        localStorage.setItem("chatHistory", chatBox.innerHTML);
    }

    function loadChatHistory() {
        let chatBox = document.querySelector("#chat-box");
        let savedHistory = localStorage.getItem("chatHistory");
        if (savedHistory) {
            chatBox.innerHTML = savedHistory;
        }
    }
</script>
<?= $this->endSection() ?>