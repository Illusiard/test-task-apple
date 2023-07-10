let csrfToken = $('meta[name="csrf-token"]').attr("content");
let vm = new Vue({
  el: '#app',
  data: {
    lastUpdate: null,
    apples: [],
    bitePercent: 25,
    count: 1,
  },
  created: function () {
    fetch('/apple/list').then(response => response.json())
      .then(function (response) {
        vm.apples = response.apples;
        vm.lastUpdate = response.date;
      })
  },
  mounted : function(){
    setTimeout(this.updates, 3000);
  },
  methods: {
    create: function () {
      fetch('/apple/create?count=' + vm.count, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json;charset=utf-8',
          'X-CSRF-Token': csrfToken
        },
        body: {count: vm.count}
      }).then(response => response.json())
        .then(function (response) {
          response.apples.forEach(element => vm.apples.push(element))
        })
    },
    bite: function (id) {
      let percent = vm.bitePercent;
      fetch('/apple/bite?id=' + id + '&percent=' + percent, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json;charset=utf-8',
          'X-CSRF-Token': csrfToken
        },
      }).then(function (response) {
          if (response.ok) {
            let apple = vm.findApple(id);
            apple.percent -= percent;
            if (apple.percent === 0) {
              apple.status = 3;
            }
          } else {
            response.json().then(json => alert(json.message));
          }
        })
    },
    drop: function (id) {
      fetch('/apple/drop?id=' + id, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json;charset=utf-8',
          'X-CSRF-Token': csrfToken
        },
      }).then(function (response) {
        if (response.ok) {
          let apple = vm.findApple(id);
          apple.status = 1;
        } else {
          response.json().then(json => alert(json.message));
        }
      })
    },
    hide: function (id) {
      fetch('/apple/delete?id=' + id, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json;charset=utf-8',
          'X-CSRF-Token': csrfToken
        },
      }).then(function (response) {
        if (response.ok) {
          let apple = vm.findApple(id);
          apple.status = 3;
        } else {
          response.json().then(json => alert(json.message));
        }
      })
    },
    updates: function () {
      fetch('/apple/updates?datetime=' + vm.lastUpdate).then(response => response.json())
        .then(function (response) {
          if (typeof response.date !== 'undefined') {
            vm.lastUpdate = response.date;
            response.apples.forEach(function(apple) {
              let vmApple = vm.findApple(apple.id);
              if (typeof vmApple === 'undefined') {
                vm.apples.push(apple);
              } else {
                for (var key in apple) {
                  if (apple.hasOwnProperty(key)) {
                    vmApple[key] = apple[key];
                  }
                }
              }
            });
          }
          vm.updateInterval = setTimeout(vm.updates, 3000);
      })
    },
    findApple: function (id) {
      for(let i = 0; i<vm.apples.length; i++) {
        if (vm.apples[i].id === id) {
          return vm.apples[i];
        }
      }
      return undefined;
    },
  }
});