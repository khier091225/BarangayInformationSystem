<x-dialog id="confirm-dialog" title="Delete record?" icon="trash-2" :compact="true" class="bis-dialog--danger" data-confirm-dialog aria-describedby="confirm-dialog-message">
    <div class="bis-dialog-body">
        <p id="confirm-dialog-message" class="bis-dialog-message" data-confirm-message></p>
        <p class="bis-dialog-warning">This action cannot be undone.</p>
    </div>
    <x-slot:footer>
        <button type="button" class="button button-outline" data-dialog-close data-dialog-initial-focus>Cancel</button>
        <button type="button" class="button bis-dialog-destructive" data-confirm-submit>Delete record</button>
    </x-slot:footer>
</x-dialog>
