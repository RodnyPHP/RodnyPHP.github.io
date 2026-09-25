const defaults={profile:{name:'RodnyPHP',bio:'Web developer and PHP enthusiast'},links:[{title:'GitHub',url:'https://github.com/RodnyPHP'}]};
const saved=JSON.parse(localStorage.getItem('portfolioData')||'null')||defaults;
document.querySelector('#bio').textContent=saved.profile.bio;
const list=document.querySelector('#link-list');
saved.links.forEach(link=>{const card=document.createElement('a');card.className='card';card.href=link.url;card.target='_blank';card.rel='noopener';card.textContent=link.title;list.appendChild(card);});
