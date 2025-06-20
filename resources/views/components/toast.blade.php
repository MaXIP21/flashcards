<div 
    x-data="toast"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    @notify.window="show($event.detail)"
    class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg text-white"
    :class="{
        'bg-green-500': type === 'success',
        'bg-red-500': type === 'error',
        'bg-blue-500': type === 'info',
        'bg-yellow-500': type === 'warning'
    }"
    style="display: none;"
>
    <div class="flex items-center">
        <div x-text="message"></div>
        <button @click="visible = false" class="ml-4 text-white hover:text-gray-200">
            &times;
        </button>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toast', () => ({
            visible: false,
            message: '',
            type: 'info',
            timeout: null,
            show(detail) {
                this.message = detail.message;
                this.type = detail.type || 'info';
                this.visible = true;

                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.visible = false;
                }, 5000);
            }
        }))
    })
</script>