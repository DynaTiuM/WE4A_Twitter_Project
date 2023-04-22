
<div id="updateMessagePopup" class="update-message-popup">
    <div class="window-content">
        <span class="close-popup">&times;</span>
        <form id="updateMessageForm" onsubmit="return false;">
            <input type="hidden" id="messageIdToUpdate" value="">
            <label for="newContent">Nouveau contenu du message:</label>
            <textarea id="newContent" class = "message-content"></textarea>
            <button onclick="submitUpdateMessageForm()">Mettre à jour</button>
        </form>
    </div>
</div>

<style>
    .update-message-popup {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .popup-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
    }

</style>