window.eventForm = function() {
  return {
    dates: [''],
    activities: [{ name:'', category:'', description:'' }],
    addDate() { this.dates.push(''); },
    removeDate(i) { if(this.dates.length>1) this.dates.splice(i,1); },
    addActivity() { this.activities.push({ name:'', category:'', description:'' }); },
    removeActivity(i) { if(this.activities.length>1) this.activities.splice(i,1); }
  };
};
