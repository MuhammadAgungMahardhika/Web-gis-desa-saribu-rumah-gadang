<?= $this->extend('maps/main'); ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container-fluid py-4">

        <div class="chat-box mb-3 p-2 border rounded bg-light" id="chat-box" style="height: 400px; overflow-y: auto;">
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

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    // Fungsi untuk menyimpan dan mengelola User ID
    const UserIdManager = {
        saveUserIdToSessionStorage: function(userId) {
            try {
                sessionStorage.setItem('user_id', userId);
                console.log('User ID berhasil disimpan di sessionStorage');
            } catch (error) {
                console.error('Gagal menyimpan User ID di sessionStorage:', error);
            }
        },

        getUserIdFromSessionStorage: function() {
            try {
                return sessionStorage.getItem('user_id') || null;
            } catch (error) {
                console.error('Gagal mengambil User ID dari sessionStorage:', error);
                return null;
            }
        }
    };
    <?php if (logged_in()) :  ?>
        UserIdManager.saveUserIdToSessionStorage('<?= user_id(); ?>')
        console.log('<?= user_id(); ?>')
        console.log('menyimpan dari user yang login')
    <?php else: ?>

        console.log('tidak ada user loggin')
    <?php endif; ?>

    // Fungsi untuk menerima User ID dari B4A WebView
    function receiveUserIdFromB4A(userId) {
        UserIdManager.saveUserIdToSessionStorage(userId);
        console.log('User ID diterima dari B4A:', userId);
    }
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
        localStorage.removeItem("chatHistory");
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
        localStorage.setItem("chatHistory", document.querySelector("#chat-box").innerHTML);
    }

    function loadChatHistory() {
        let savedHistory = localStorage.getItem("chatHistory");
        if (savedHistory) {
            document.querySelector("#chat-box").innerHTML = savedHistory;
        }
    }
</script>
<?= $this->endSection() ?>