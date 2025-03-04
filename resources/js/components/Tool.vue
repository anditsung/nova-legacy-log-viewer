<template>
  <div class="space-y-4">
    <div class="flex items-center mb-8">
      <heading :level="1">
        {{ __('Log Viewer') }}
      </heading>

      <div class="flex flex-grow justify-end items-center">

        <div class="flex items-center mr-3">
          <select-control
              v-if="logs.length > 1"
              v-model="selectedLogFile"
              @change="handleLogChange"
              :options="logs"
              size="sm"
          >
            <option value="" selected disabled>
              {{ __('Select a log file...') }}
            </option>
          </select-control>
          <p v-else-if="logs.length == 1" class="font-bold truncate">
            {{ selectedLogFile }}
          </p>
          <p v-else>
            &mdash;
          </p>
        </div>

        <button class="rounded flex flex-no-shrink focus:shadow-outline focus:outline-none mr-3"
                @click="replaceContent">
          <svg class="p-1 w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
          </svg>
        </button>

        <button class="rounded flex flex-no-shrink focus:shadow-outline focus:outline-none mr-3"
                @click="scrollToTop">
          <svg class="p-1 w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
          </svg>
        </button>

        <button class="rounded flex flex-no-shrink focus:shadow-outline focus:outline-none"
                @click="scrollToBottom">
          <svg class="p-1 w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
          </svg>
        </button>
      </div>
    </div>

    <card class="bg-gray-900 text-gray-300 overflow-hidden mb-8">
      <textarea ref="viewer" />
    </card>

    <p class="text-xs uppercase font-bold tracking-widest">
      {{ __(`:number ${lineLabel}`, { number: numberOfLines }) }}
    </p>
  </div>
</template>

<script>
import CodeMirror from 'codemirror'
import isNil from 'lodash/isNil'

export default {
  codemirror: null,

  data: () => ({
    logs: [],
    playing: false,
    content: null,
    lastLine: 0,
    interval: null,
    selectedLogFile: null,
    numberOfLines: 0,
    scrolledToBottom: false,
  }),

  mounted() {
    this.setupCodeMirror()
    this.fetchLogs()
  },

  beforeUnmount() {
    clearInterval(this.interval)
    this.codemirror = null
  },

  methods: {

    async fetchLogs() {
      const { data } = await Nova.request('/nova-vendor/legacy-logs')
      this.logs = data
      this.selectFirstLog()
      this.replaceContent()
    },

    setupCodeMirror() {
      this.codemirror = CodeMirror.fromTextArea(this.$refs.viewer, {
        tabSize: 4,
        indentWithTabs: true,
        lineWrapping: false,
        lineNumbers: true,
        theme: 'dracula',
        readOnly: true,
      })
      this.codemirror?.setSize('100%', 'calc(100vh - 19rem)')

      this.codemirror.on('scroll', this.updateScrollPosition)
    },

    selectFirstLog() {
      this.selectedLogFile = this.logs[0].value
    },

    updateScrollPosition() {
      const scrollInfo = this.codemirror.getScrollInfo()

      this.scrolledToBottom =
          scrollInfo.top >= scrollInfo.height - scrollInfo.clientHeight

      if (!this.scrolledToBottom) {
        this.playing = false
      }
    },

    handleLogChange(option) {
      this.replaceContent()
    },

    requestContent() {
      return Nova.request().get('/nova-vendor/legacy-logs/log', {
        params: { log: this.selectedLogFile, lastLine: this.lastLine },
      })
    },

    fetchContent() {
      if (isNil(this.selectedLogFile)) {
        return
      }

      this.requestContent().then(
          ({ data: { content, lastLine, numberOfLines } }) => {
            this.lastLine = lastLine
            this.content = this.content += content
            this.numberOfLines = numberOfLines
            this.codemirror?.replaceRange(
                content,
                CodeMirror.Pos(this.codemirror?.lastLine() - 1)
            )
          }
      )
    },

    replaceContent() {
      this.lastLine = 0

      if (isNil(this.selectedLogFile)) {
        return
      }

      this.requestContent().then(
          ({ data: { content, lastLine, numberOfLines } }) => {
            this.lastLine = lastLine
            this.content = content
            this.numberOfLines = numberOfLines
            this.codemirror?.getDoc().setValue(this.content)
          }
      )
    },

    scrollToTop() {
      this.$nextTick(() => this.codemirror?.setCursor(0, 0))
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const scrollInfo = this.codemirror.getScrollInfo()
        this.codemirror.scrollTo(0, scrollInfo.height)
      })
    },
  },

  computed: {
    lineLabel() {
      return this.numberOfLines === 1
        ? 'line'
        : 'lines'
    },
  },
}
</script>
