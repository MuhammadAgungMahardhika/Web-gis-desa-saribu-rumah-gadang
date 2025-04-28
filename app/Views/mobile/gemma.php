<?= $this->extend('web/layouts/main'); ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container-fluid p-4 px-0 chat-page-container" style="height: calc(100vh - 56px);">

        <div class="chat-box mb-3 p-2 border rounded bg-light chat-history-area" id="chat-box" style="overflow-y: auto;">
            <p class="text-muted text-center">Percakapan akan muncul di sini...</p>
        </div>

        <div class="input-group">
            <input type="text" id="input" class="form-control border rounded-2" placeholder="Ketik pesan..." style="box-shadow: none;">
            <button class="btn btn-primary" id="send-btn" style="box-shadow: none;">Kirim</button>
            <button class="btn btn-light border ms-2 p-2" id="clear-chat" style="box-shadow: none;" title="hapus riyawat chat">
                🗑
            </button>
        </div>

    </div>
</section>

<style>
    /* Pastikan CSS ini juga ada */
    .section {
        min-height: 100vh;
        /* Optional: Ensure section takes full height */
        display: flex;
        flex-direction: column;
    }

    .chat-page-container {
        display: flex;
        flex-direction: column;
        /* height: calc(100vh - 56px); <--- ini sudah di inline style atau bisa juga di sini */
    }

    .chat-history-area {
        flex-grow: 1;
        overflow-y: auto;
        /* height: 400px; <--- hapus atau timpa ini */
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    <?php if (logged_in()) :  ?>
        UserIdManager.saveUserIdToSessionStorage('<?= user_id(); ?>')
        console.log('<?= user_id(); ?>')
        console.log('menyimpan dari user yang login')
    <?php else: ?>

        console.log('tidak ada user loggin')
    <?php endif; ?>

    document.addEventListener("DOMContentLoaded", function() {
        loadChatHistory();
    });

    document.querySelector("#send-btn").addEventListener("click", sendMessage);
    document.querySelector("#input").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    });

    async function sendMessage() {
        let inputField = document.querySelector("#input");
        let message = inputField.value.trim();
        if (!message) return;

        appendMessage("user", message);
        inputField.value = "";
        // Ambil User ID dari localStorage
        const userId = UserIdManager.getUserIdFromSessionStorage();
        console.log(userId)
        let response = await fetch("<?= site_url('mobile/gemma/processRequest') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `message=${encodeURIComponent(message)}&userId=${encodeURIComponent(userId || '')}`
        });

        let result = await response.json();
        appendMessage(result.error ? "error" : "ai", result.error ? "Error: " + result.error : result.response);

        saveChatHistory();
    }

    document.querySelector("#clear-chat").addEventListener("click", async function() {
        document.querySelector("#chat-box").innerHTML = '<p class="text-muted text-center">Percakapan akan muncul di sini...</p>';
        await fetch("<?= site_url('mobile/gemma/resetChat') ?>", {
            method: "GET"
        });
        sessionStorage.removeItem("chatHistory");
    });

    function appendMessage(role, message) {
        let chatBox = document.querySelector("#chat-box");
        let messageElement = document.createElement("div");

        messageElement.classList.add("p-3", "border", "mb-2", "rounded");

        messageElement.style.backgroundColor = "#ffffff";
        messageElement.style.color = "#000000";

        if (role === "user") {
            messageElement.classList.add("border-primary", "text-end");
        } else if (role === "ai") {
            messageElement.classList.add("border-secondary");
        } else {
            messageElement.classList.add("border-danger");
        }

        messageElement.innerHTML = message; // Ganti textContent dengan innerHTML
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function saveChatHistory() {
        sessionStorage.setItem("chatHistory", document.querySelector("#chat-box").innerHTML);
    }

    function loadChatHistory() {
        let savedHistory = sessionStorage.getItem("chatHistory");
        if (savedHistory) {
            document.querySelector("#chat-box").innerHTML = savedHistory;
        }
    }
</script>
<?= $this->endSection() ?>