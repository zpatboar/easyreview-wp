<strong>* Marks a required fields</strong>
<div style="color: red; line-height: 1.4em; padding-bottom: 10px;">
    <?php if ($formvalidated !== false){
        echo $formvalidated;
    }?>
</div>

<form method="POST">
    <fieldset>
        <legend>Enter Your Contact Information</legend>
        <label for="your_name">* Your Name</label>
        <input name="your_name" id="your_name" type="text"  value="<?php echo (isset($iPOST['your_name']) ? htmlentities($iPOST['your_name']) : "" ); ?>">
        <label for="your_email">* Your Email</label>
        <input name="your_email" id="your_email" type="text" value="<?php echo (isset($iPOST['your_email']) ? htmlentities($iPOST['your_email']) : "" ); ?>">
        <label for="your_phone">* Your Phone</label>
        <input name="your_phone" id="your_phone" type="tel" value="<?php echo (isset($iPOST['your_phone']) ? htmlentities($iPOST['your_phone']) : "" ); ?>">
    </fieldset>
    <fieldset>
        <legend>Enter Your Review</legend>
        <label for="stars">* On a Scale of 1-5, with 5 being the best, how satisfied are you with us</label>
        <br />
        <input type="radio" name="stars" value="1" <?php echo (isset($iPOST['stars']) && $iPOST['stars'] == 1 ? "checked" : "" ); ?>> 1 &nbsp;
        <input type="radio" name="stars" value="2" <?php echo (isset($iPOST['stars']) && $iPOST['stars'] == 2 ? "checked" : "" ); ?>> 2 &nbsp;
        <input type="radio" name="stars" value="3" <?php echo (isset($iPOST['stars']) && $iPOST['stars'] == 3 ? "checked" : "" ); ?>> 3 &nbsp;
        <input type="radio" name="stars" value="4" <?php echo (isset($iPOST['stars']) && $iPOST['stars'] == 4 ? "checked" : "" ); ?>> 4 &nbsp;
        <input type="radio" name="stars" value="5" <?php echo (isset($iPOST['stars']) && $iPOST['stars'] == 5 ? "checked" : "" ); ?>> 5
        <br />
        <label for="title">Enter an Optional Title for your Review</label>
        <input name="title" id="title" type="text" value="<?php echo (isset($iPOST['title']) ? htmlentities($iPOST['title']) : "" ); ?>">
        <br />
        <label for="review">* Enter your review or any comments below</label>
        <textarea name="review" id="review" style="width: 100%;" rows="5"><?php echo (isset($iPOST['review']) ? htmlentities($iPOST['review']) : "" ); ?></textarea>
    </fieldset>
    <label>* What is two plus two</label>
    <br />
    <input name="q4oij32r" type="text">
    <button type="submit">Submit Review</button>
</form>

