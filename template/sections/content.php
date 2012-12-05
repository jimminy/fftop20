  <section class="container">
    <header class="sixteen columns">
      <h1 class="remove-bottom" style="margin-top: 20px">Friendfeed Top 20</h1>
      <h5>An alternative to the fftopten.</h5>
      <hr />
    </header>
    <section class="two-thirds column">
      <?php echo $content; ?>
    </section>
    <sidebar class="one-third column">
      <nav>
        <?php if(isset($logged_in)):?>
        <h3>Share to Friendfeed</h3>
        <hr />
          <section class="one-third column alpha">
            <form id="p2ff" method="post">
              <label for="title">Title</label>
              <textarea class="one-third column alpha" id="title">#fftop20</textarea>
              <label for="comments">Comment Body</label>
              <textarea class="one-third column alpha" id="comments"><?php echo $comment; ?></textarea>
              <input type="hidden" id="photos" value=<?php echo'"'.$photo_array.'"'; ?> />
            <input id="p2ffb" type="submit" onclick="ajax_post('lib/api.php', 'p2ff', document.getElementById('title').value, document.getElementById('comments').value, document.getElementById('photos').value), remove_element('p2ff','p2ffb');" value ="Post to Friendfeed" />
          </form>
        </section>
        <h3>Options</h3>
        <hr />
        <ul>
          <a href="clearsessions.php"><h5>Logout</h5></a>
        </ul>
        <?php endif; ?>
        <h3>Privacy</h3>
        <hr />
        <p>No data is stored outside of session data, all information will be removed when you close your browser or logout. No data will be posted to your account should you not willingly submit it.</p>
        <h3>Information</h3>
        <hr />
        <p>This service allows you to see those people that have the highest bidirectional interaction levels with you. It is measured using 3 data sources:
        <ul class="square">
          <li>Your last 300 posts. A user's likes are counted once. A user's comments are counted twice.</li>
          <li>Your last 300 likes. The user who posted content you liked will be counted once.</li>
          <li>Comments you made on other user's posts. Uses the last 300 posts you commented on. Every comment is counted twice, and provided that user.</li>
        </ul>
        <p>Feedback at <a href="http://friendfeed.com/25hrlabs">25hrlabs on Friendfeed</a></p>
        <p style="margin-top: -10px">Copyright 2011 by <a href="http://friendfeed.com/jimminy">Jimminy</a></p>
      </nav>
    </sidebar>
  </section>