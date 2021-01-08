import Axios from 'axios'
import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'

Vue.use(Vuex)

const vuexLocal = new VuexPersistence({
  storage: window.localStorage
})

export default new Vuex.Store({
  state:  {
    session: null, // Session object
    quizzes: [], // List of quiz ids
    answers: [], // List of choice ids
  },
  mutations: {
    SET_SESSION(state, session) {
      state.session = session
    },
    SET_QUIZZES(state, quizzesIds) {
      state.quizzes = quizzesIds;
    },
    SET_ANSWERS(state, {index, choiceId}) {
      Vue.set(state.answers, index, choiceId)
    },
    CLEAR(state) {
      state.session = null
      state.quizzes = []
      state.answers = []
    }
  },
  actions: {
    async finishSession({state, commit}) {
      try {
        const res = await Axios.post(`/sessions/${state.session.code}`, {
          quizzes: state.quizzes,
          answers: state.answers,
        })

        commit('CLEAR', null)

        return Promise.resolve(res)
      } catch (err) {
        return Promise.reject(err)
      }
    }
  },
  plugins: [vuexLocal.plugin]
})