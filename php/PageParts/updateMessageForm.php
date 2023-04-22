
<div id="updateMessagePopup" class="window-background">
    <div class="window-content" style = "text-align: center">
        <span class="close" onclick="closeWindow('updateMessagePopup')">&times;</span>
        <form id="updateMessageForm" onsubmit="return false;">
            <input type="hidden" id="messageIdToUpdate" value="">
            <label for="newContent" class = "window-title">Modification du message</label>
            <textarea id="newContent" class = "message-content" style = "margin-top: 2vw; padding-top: 1vw; border-top: 0.1vw solid rgba(31, 124, 98, 0.29); border-bottom: 0.1vw solid rgba(31, 124, 98, 0.29);"></textarea>
            <button onclick="submitUpdateMessageForm()" class = "tweet-button" style = "border:none">Mettre à jour</button>
        </form>
    </div>
</div>