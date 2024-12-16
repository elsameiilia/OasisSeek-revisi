<?php

if (!session_id())
session_start();
include_once __DIR__ . "/database/database.php";

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8"/>
        <title>About OasisSeek</title>
        <link rel="stylesheet" type="text/css" href="/images/assets/styles.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>

  <body>
    <div class="about-container">
   <!-- ======== HEADER ======== -->

   <?php include_once __DIR__. "/template/navbar.php"; ?>

    <!-- ======== BANNER HERO ABOUT ======== -->
    <section class="hero-section-about">
        <img src="/images/assets/about-banner.png" alt="About section hero image" class="hero-image-about" />
        <h1 class="hero-title-about">About</h1>
        <p class="hero-description-about">
          <b>OasisSeek</b> is your ultimate guide to discovering breathtaking travel destinations and upcoming events. Explore a curated collection of exotic spots and must-visit attractions tailored for your next adventure. Stay updated with our event highlights, featuring cultural festivals, thrilling activities, and local experiences happening near your chosen destinations. Whether you're planning a quick getaway or a dream vacation, OasisSeek makes it effortless to find inspiration and create unforgettable memories.
        </p>
      </section>
    
  <!-- ======== CONTACT OASIS SEEK ======== -->
      <section class="contact-section-about">
        <div class="contact-container-about">
          <div class="contact-info-about">
            <h2 class="contact-title-about">Our Contact</h2>
            <p class="contact-subtitle-about">
              If you have any questions, critique, or advice regarding us and our website, don't hesitate to contact us!
            </p>
            <div class="contact-details-about">
              <div class="contact-item-about">
                <img src="/images/assets/loc-icon-about.png" alt="Location icon" class="contact-icon-about" />
                <span>Blater, Purbalingga</span>
              </div>
              <div class="contact-item-about">
                <img src="/images/assets/call-icon-about.png" alt="Phone icon" class="contact-icon-about" />
                <span>+62 8123 4567 890</span>
              </div>
              <div class="contact-item-about">
                <img src="/images/assets/mail-icon-about.png" alt="Email icon" class="contact-icon-about" />
                <span>oasisseek@gmail.com</span>
              </div>
            </div>
            
            <div class="social-section-about">
              <h3 class="social-title-about">Follow us on social media:</h3>
              <div class="social-links-about">
                <div class="social-item-about">
                  <img src="/images/assets/insta-icon-about.png" alt="Twitter icon" class="social-icon-about" />
                  <span>@oasisseek1</span>
                </div>
                <div class="social-item-about">
                  <img src="/images/assets/facebook-icon-about.png" alt="Facebook icon" class="social-icon-about" />
                  <span>OasisSeek Official</span>
                </div>
                <div class="social-item-about">
                  <img src="/images/assets/tiktok-icon-about.png" alt="Instagram icon" class="social-icon-about" />
                  <span>@oasisseek1</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    
    
      <!--============== DROPDOWN FAQ=================== -->

      <div class="faq-container">
        <div class="header-wrapper-faq">
          <h1 class="main-title">Any questions?</h1>
          <h2 class="subtitle">Frequently Asked Questions (FAQ)</h2>
        </div>
      
        <div class="questions-wrapper">
          <div class="question-item">
            <button class="question-toggle">
              What is this website about?
            </button>
            <div class="answer">
              <p>
                Our website provides information on Egypt's top destinations, including historical landmarks, cultural attractions, and travel tips to help you plan your trip.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
      
          <div class="question-item">
            <button class="question-toggle">
              How do I get around in Egypt?
            </button>
            <div class="answer">
              <p>
                Egypt offers various transportation options such as taxis, buses, and trains. Domestic flights are also available for traveling between major cities.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
      
          <div class="question-item">
            <button class="question-toggle">
              Is Egypt affordable for travelers?
            </button>
            <div class="answer">
              <p>
                Yes, Egypt is considered a budget-friendly destination with many affordable accommodation and dining options.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
      
          <div class="question-item">
            <button class="question-toggle">
              What activities can I do in Egypt?
            </button>
            <div class="answer">
              <p>
                You can explore the pyramids, cruise the Nile River, visit ancient temples, enjoy snorkeling in the Red Sea, and more.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
        </div>
      </div>
      
      
    <!-- ========= JS untuk dropdown =========-->
    <script>
      document.querySelectorAll('.question-toggle').forEach((button) => {
      button.addEventListener('click', () => {
        const answer = button.nextElementSibling;

        // Toggle menampilkan jawaban
        button.classList.toggle('active');
        if (answer.style.display === 'block') {
          answer.style.display = 'none';
        } else {
          answer.style.display = 'block';
        }
      });
    });
    </script>
     
    <!-- =========== FOOTER =========== -->
  
    <?php include_once __DIR__ . "/template/footer.php"; ?>

  </div>
</body>
</html>
