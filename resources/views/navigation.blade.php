<router-link
        tag="h3"
        :to="{
            name: 'nova-legacy-log-viewer',
            params: {
                logs: {{ \Anditsung\NovaLegacyLogViewer\LogViewer::logs() }}
            }
        }"
        class="cursor-pointer flex items-center font-normal dim text-white mb-6 text-base no-underline">
    <svg class="sidebar-icon"
         xmlns="http://www.w3.org/2000/svg"
         fill="none"
         viewBox="0 0 24 24">
        <path
                fill="var(--sidebar-icon)" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
    </svg>
    <span class="sidebar-label">
        {{ __('Logs') }}
    </span>
</router-link>
