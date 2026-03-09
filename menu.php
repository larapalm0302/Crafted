 <menu class="menu-invisible">
     <div class="menu-buttons">
         <button class="menu-button largeW" delay="0.15s" onclick="window.location.href='/'">Home</button>
         <button class="menu-button largeW" delay="0.30s" onclick="window.location.href='/nieuws'">Nieuws</button>
         <button class="menu-button largeW" delay="0.45s" onclick="window.location.href='/contact'">Contact</button>
         <button class="menu-button largeW largeH" delay="0.60s" onclick="window.location.href='/crafted-friends'">
             Crafted<br>
             <span class="smallFont">&</span><br>
             Friends
         </button>
         <button class="menu-button largeW" delay="0.75s" onclick="window.location.href='/tickets'">Tickets</button>
         <button class="menu-button largeW" delay="0.90s" onclick="window.location.href='/programma'">Programma</button>
     </div>
     <div class="info-container">
         <div class="info">
             <!-- Lorem ipsum dolor sit amet, -->
             <p>Lorem ipsum dolor sit amet, .consectetur adipiscing elit. In nibh libero, feugiat ac laoreet ac, dictum mollis turpis. Donec euismod sapien non nisl porta, at dapibus mauris laoreet. Duis in tincidunt justo, rhoncus ultrices libero. Proin sodales suscipit ex eget consequat. Pellentesque at orci efficitur, molestie risus in, porta diam. Phasellus ac efficitur metus, a fermentum leo. Maecenas posuere luctus urna et vehicula. Vestibulum et quam in orci cursus interdum non at leo. Nunc non ante ultricies, viverra dui eu, interdum elit.</p>
         </div>
         <div class="socials">
             <?php
             $socials = [
                 'instagram' => [
                     'url' => get_option('crafted_social_insta'),
                     'delay' => '0.15s',
                     'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 51 51" class="icon"><path d="M36.125 4.25H14.875C9.00697 4.25 4.25 9.00697 4.25 14.875V36.125C4.25 41.993 9.00697 46.75 14.875 46.75H36.125C41.993 46.75 46.75 41.993 46.75 36.125V14.875C46.75 9.00697 41.993 4.25 36.125 4.25Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M34.0001 24.1613C34.2624 25.9298 33.9603 27.736 33.1368 29.3229C32.3134 30.9099 31.0105 32.1968 29.4136 33.0006C27.8166 33.8044 26.0068 34.0842 24.2417 33.8001C22.4765 33.5161 20.8459 32.6827 19.5816 31.4185C18.3174 30.1543 17.484 28.5236 17.2 26.7585C16.916 24.9933 17.1957 23.1836 17.9996 21.5866C18.8034 19.9896 20.0903 18.6867 21.6772 17.8633C23.2641 17.0399 25.0703 16.7378 26.8389 17C28.6428 17.2675 30.3129 18.1081 31.6025 19.3977C32.892 20.6872 33.7326 22.3573 34.0001 24.1613Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M37.1875 13.8125H37.2081" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>'
                 ],
                 'linkedin' => [
                     'url' => get_option('crafted_social_linkedin'),
                     'delay' => '0.30s',
                     'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 52 52" class="icon"><g clip-path="url(#clip0)"><path d="M34.6667 17.3333C38.1146 17.3333 41.4212 18.703 43.8591 21.1409C46.2971 23.5789 47.6667 26.8855 47.6667 30.3333V45.5H39.0001V30.3333C39.0001 29.184 38.5435 28.0818 37.7309 27.2692C36.9182 26.4565 35.816 26 34.6667 26C33.5175 26 32.4153 26.4565 31.6026 27.2692C30.79 28.0818 30.3334 29.184 30.3334 30.3333V45.5H21.6667V30.3333C21.6667 26.8855 23.0364 23.5789 25.4744 21.1409C27.9123 18.703 31.2189 17.3333 34.6667 17.3333Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M12.9999 19.5H4.33325V45.5H12.9999V19.5Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M8.66659 13C11.0598 13 12.9999 11.0599 12.9999 8.66665C12.9999 6.27341 11.0598 4.33331 8.66659 4.33331C6.27335 4.33331 4.33325 6.27341 4.33325 8.66665C4.33325 11.0599 6.27335 13 8.66659 13Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></g><defs><clipPath id="clip0"><rect width="52" height="52" /></clipPath></defs></svg>'
                 ],
                 'tiktok' => [
                     'url' => get_option('crafted_social_tiktok'),
                     'delay' => '0.45s',
                     'svg' => '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 682.667 682.667" class="my-icon"><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" /></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c-62.115 0-112.467 50.353-112.467 112.466h-80.334V-216.9c0-39.93-32.369-72.3-72.301-72.3-39.929 0-72.298 32.37-72.298 72.3 0 39.929 32.369 72.299 72.298 72.299v80.334c-84.294 0-152.632-68.337-152.632-152.633 0-84.297 68.338-152.634 152.632-152.634 84.301 0 152.635 68.337 152.635 152.634v172.761C-80.811-66.912-41.975-80.334 0-80.334Z" transform="translate(464.867 384.534)" fill="none" stroke="currentColor" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" /></g></g></svg>'
                 ],
                 'youtube' => [
                     'url' => get_option('crafted_social_youtube'),
                     'delay' => '0.60s',
                     'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 51 51" class="icon"><path d="M5.31258 36.125C3.8281 29.1194 3.8281 21.8806 5.31258 14.875C5.50763 14.1636 5.88449 13.5152 6.40611 12.9935C6.92774 12.4719 7.57615 12.095 8.28758 11.9C19.6849 10.0118 31.3152 10.0118 42.7126 11.9C43.424 12.095 44.0724 12.4719 44.594 12.9935C45.1157 13.5152 45.4925 14.1636 45.6876 14.875C47.1721 21.8806 47.1721 29.1194 45.6876 36.125C45.4925 36.8364 45.1157 37.4848 44.594 38.0065C44.0724 38.5281 43.424 38.9049 42.7126 39.1C31.3153 40.9885 19.6849 40.9885 8.28758 39.1C7.57615 38.9049 6.92774 38.5281 6.40611 38.0065C5.88449 37.4848 5.50763 36.8364 5.31258 36.125Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M21.25 31.875L31.875 25.5L21.25 19.125V31.875Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>'
                 ]
             ];

             foreach ($socials as $key => $data):
                 if ($data['url']):
                     ?>
             <a href="<?php echo esc_url($data['url']); ?>" target="_blank" class="social-image-container" delay="<?php echo esc_attr($data['delay']); ?>" aria-label="<?php echo esc_attr(ucfirst($key)); ?>">
                 <?php echo $data['svg']; ?>
             </a>
             <?php endif; endforeach; ?>
         </div>
     </div>
 </menu>