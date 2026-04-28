/**
 * Singleton global loading manager.
 * Tracks concurrent loading operations; sets cursor:wait while any are active.
 */

import { ref } from 'vue'

// Module-level counter – all component instances share the same state
let _count = 0
const isGlobalLoading = ref(false)

function start() {
  _count++
  isGlobalLoading.value = true
  document.body.style.cursor = 'wait'
}

function stop() {
  _count = Math.max(0, _count - 1)
  if (_count === 0) {
    isGlobalLoading.value = false
    document.body.style.cursor = ''
  }
}

/**
 * Wraps an async function ensuring start/stop are always called in pair.
 * @param {() => Promise<any>} fn
 */
async function withLoading(fn) {
  start()
  try {
    return await fn()
  } finally {
    stop()
  }
}

export function useLoading() {
  return { isGlobalLoading, start, stop, withLoading }
}
