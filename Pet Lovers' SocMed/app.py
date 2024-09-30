import os

from cs50 import SQL
from flask import Flask, redirect, render_template, request, session, jsonify
from flask_session import Session
from werkzeug.security import check_password_hash, generate_password_hash
import uuid
from helpers import login_required

# Configure application
app = Flask(__name__)


# Configure session to use filesystem (instead of signed cookies)
app.config["SESSION_PERMANENT"] = False
app.config["SESSION_TYPE"] = "filesystem"
Session(app)

# Configure CS50 Library to use SQLite database
db = SQL("sqlite:///ppl.db")


@app.after_request
def after_request(response):
    """Ensure responses aren't cached"""
    response.headers["Cache-Control"] = "no-cache, no-store, must-revalidate"
    response.headers["Expires"] = 0
    response.headers["Pragma"] = "no-cache"
    return response


@app.route("/")
def index():
    tops = db.execute("SELECT profilepicfilename, username, name, loves_earned FROM users ORDER BY loves_earned DESC LIMIT 3")
    top1 = tops[0]
    try:
        top2 = tops[1]
    except:
        top2 = None
    try:
        top3 = tops[2]
    except:
        top3 = None

    #manage the activities
    activities = db.execute('SELECT * FROM activities ORDER BY time DESC')

    return render_template("index.html", top1=top1, top2=top2, top3=top3, activities=activities)

@app.route("/search")
@login_required
def search():
    
    name = request.args.get('q')
    pass_name = f"%{name}%"
    get_data_searched = db.execute("SELECT * FROM users WHERE name LIKE ?", pass_name)
    #retain searched text
    retain_searched_text = name
    return render_template('search.html', get_data_searched=get_data_searched, retain_searched_text=retain_searched_text)

@app.route("/profile", methods=["GET", "POST"])
@login_required
def profile():
    """View profile"""
    if request.method == "POST":
        pass

    username = request.args.get("n")
    if username == session["username"]:
        my_own_profile = True
        session["visit_profile_username"] = None
    else:
        my_own_profile = False
        session["visit_profile_username"] = username
    #get pets info
    get_pet_infos = db.execute("SELECT id, name, species, petprofilefilename, reactcount FROM pets WHERE user_id = ?", db.execute("SELECT id FROM users WHERE username = ?", username)[0]["id"])
    #set how many number of pets the profile have
    db.execute("UPDATE users SET num_of_pets = ? WHERE username = ?", len(get_pet_infos), username)
    #get user's info
    get_items = db.execute("SELECT id, profilepicfilename, name, num_of_pets, followers_count, following_count, love_tokens, loves_earned FROM users WHERE username = ?", username)
    profile_infos = dict(get_items[0])

    check_followed = db.execute("SELECT * FROM followers WHERE user_id = ? AND followers_id = ?", profile_infos['id'], session['user_id'])
    
    if check_followed:
        followed = True
    else:
        followed = False
    return render_template("profile.html", profile_infos=profile_infos, get_pet_infos=get_pet_infos, my_own_profile=my_own_profile, followed=followed)

@app.route("/messages-inbox", methods=["GET"])
@login_required
def messages():
    messages_from = db.execute("SELECT * FROM messenger_info WHERE receiver_id = ? ORDER BY time DESC", session['user_id'])

    #print(f'{messages_from}')
    #print(f'{messages_to}')
    return render_template('messages-inbox.html', messages_from=messages_from)

@app.route("/messages-sent", methods=["GET"])
@login_required
def messages_sent():
    messages_to = db.execute("SELECT * FROM messenger_info WHERE sender_id = ? ORDER BY time DESC", session['user_id'])

    #print(f'{messages_from}')
    #print(f'{messages_to}')
    return render_template('messages-sent.html', messages_to=messages_to)

@app.route("/message", methods=["POST"])
@login_required
def message():
    data = request.get_json()
    """ 
    JSON data {
        text_message, receiver_id
    }
    """
    check_usernamevsid = db.execute("SELECT username FROM users WHERE id = ?", data['receiver_id'])[0]['username']
    print(f'{check_usernamevsid}')

    if not data['text_message']:
        return data
    
    if session['visit_profile_username'] != check_usernamevsid:
        return render_template('error.html')

    #insert the text_message into


    sender_infos = db.execute('SELECT id, profilepicfilename, username, name FROM users WHERE id = ?', session['user_id'])[0]
    sender_id = sender_infos['id']
    sender_profile_pic = sender_infos['profilepicfilename']
    sender_username = sender_infos['username']
    sender_name = sender_infos['name']
    
    receiver_infos = db.execute('SELECT id, profilepicfilename, username, name FROM users WHERE id = ?', data["receiver_id"])[0]
    receiver_id = receiver_infos['id']
    receiver_profile_pic = receiver_infos['profilepicfilename']
    receiver_username = receiver_infos['username']
    receiver_name = receiver_infos['name']
    

    db.execute('INSERT INTO messenger_info (sender_id, sender_profile_pic, sender_username, sender_name, receiver_id, receiver_profile_pic, receiver_username, receiver_name, text_message) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', sender_id, sender_profile_pic, sender_username, sender_name, receiver_id, receiver_profile_pic, receiver_username, receiver_name, data['text_message'])
    return data

@app.route("/give_love", methods=["POST"])
@login_required
def give_love():
    """Give love tokens to a pet/user"""
    #if method POST
    pet_id = request.get_json()
    user_id = session["user_id"]
    print(f"\n{pet_id}\n")

    check_owner_if_self = db.execute("SELECT user_id FROM pets WHERE id = ?", pet_id)

    if check_owner_if_self:
        if check_owner_if_self[0]['user_id'] == user_id:
            print("You cannot give your own pet a token.")
        else:
            print("React Counts Updated")
            #get the users current token count
            check_users_tokencount = db.execute("SELECT love_tokens FROM users WHERE id = ?", user_id)[0]['love_tokens']
            if check_users_tokencount == 0 or check_users_tokencount < 0:
                return jsonify({
                    'insufficient_token': 1,
                })
            else:
                #update love_tokens count
                db.execute("UPDATE users SET love_tokens = ? - ? WHERE id = ?", check_users_tokencount, 1, user_id)
                db.execute("UPDATE pets SET reactcount = reactcount + ? WHERE id = ?", 1, pet_id)
                react_new_count = db.execute("SELECT reactcount FROM pets WHERE id = ?", pet_id)[0]['reactcount']

                #get the id of the pet owner
                get_id_pet_owner = db.execute("SELECT user_id FROM pets WHERE id = ?", pet_id)[0]['user_id']
                #update the love_earned of the pet owner
                get_owner_loves_earned = db.execute("SELECT loves_earned FROM users WHERE id = ?", get_id_pet_owner)[0]['loves_earned']
                db.execute("UPDATE users SET loves_earned = ? + ? WHERE id = ?", get_owner_loves_earned, 1, get_id_pet_owner)
                #get the new count of loves_earned of pet owner
                loves_earned_new_count = db.execute("SELECT loves_earned FROM users WHERE id = ?", get_id_pet_owner)[0]['loves_earned']
                print(f"{loves_earned_new_count}")
                #insert into activities table
                user_id_pic = db.execute('SELECT profilepicfilename FROM users WHERE id = ?', session['user_id'])[0]['profilepicfilename']
                user_id_username = db.execute('SELECT username FROM users WHERE id = ?', session['user_id'])[0]['username']
                user_id_name = db.execute('SELECT name FROM users WHERE id = ?', session['user_id'])[0]['name']
                target_id_username = db.execute('SELECT username FROM users WHERE id = ?', db.execute('SELECT user_id FROM pets WHERE id = ?', pet_id)[0]['user_id'])[0]['username']
                target_id_name = db.execute('SELECT name FROM users WHERE id = ?', db.execute('SELECT user_id FROM pets WHERE id = ?', pet_id)[0]['user_id'])[0]['name']
                target_id_petname = db.execute('SELECT name FROM pets WHERE id = ?', pet_id)[0]['name']
                
                db.execute('INSERT INTO activities (user_id, user_id_pic, user_id_username, user_id_name, target_id_username, target_id_name, target_id_petname, action) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', session['user_id'], user_id_pic, user_id_username, user_id_name, target_id_username, target_id_name, target_id_petname, 'givelove')

                

    else:
        print("Something went wrong!")

    return jsonify({
        'insufficient_token': False,
        'react_new_count': react_new_count,
        'loves_earned_new_count': loves_earned_new_count,
        'pet_id': str(pet_id)
    }) 

    



@app.route("/account")
@login_required
def account():
    return render_template("account.html")

@app.route("/update_profile", methods=["GET", "POST"])
@login_required
def update_profile():
    #IF POST
    if request.method == "POST":
        if 'file' not in request.files:
            return redirect("/profile")

        file = request.files['file']
        file_name = file.filename

    
        if not file_name:
            return apology("way files")
        
        #check if ang type sa file kay .jpg
        _, file_extension = os.path.splitext(file_name)
        file_extension.lower()
        if file_extension not in ['.jpg', '.jpeg']:
            session["error_fileupload"] = True
            folder_name = 'images_profiles/'
            get_filename = db.execute("SELECT profilepicfilename FROM users WHERE id = ?", session["user_id"])[0]['profilepicfilename']
            profile_picture_filename = folder_name+get_filename
            return render_template("update_profile.html", profile_picture=profile_picture_filename)

        #get user's username for profile picture naming
        username = db.execute("SELECT username FROM users WHERE id = ?", session["user_id"])[0]["username"]
        user_filename = username+'.jpg'

        #check if the user's current profile picture is not default, if it's not delete it from storage.
        get_current_filename = db.execute("SELECT profilepicfilename FROM users WHERE username = ?", username)[0]["profilepicfilename"]
        filepath = os.path.join('static/images_profiles/', user_filename) #get the path
        if get_current_filename != 'defaultprofile.jpg':
            os.remove(filepath)

        #update profilepicfilename from users table
        db.execute("UPDATE users SET profilepicfilename = ? WHERE id = ?", user_filename, session["user_id"])

        if file and file_name:
            # Save file to the upload folder
            file.save(filepath)

        return redirect("/update_profile")
    #IF GET
    else:
        session["error_fileupload"] = False
        folder_name = 'images_profiles/'
        get_filename = db.execute("SELECT profilepicfilename FROM users WHERE id = ?", session["user_id"])[0]['profilepicfilename']
        profile_picture_filename = folder_name+get_filename
        return render_template("update_profile.html", profile_picture=profile_picture_filename)


@app.route("/update_fullname", methods=["GET", "POST"])
@login_required
def update_fullname():
    #if POST
    if request.method == "POST":
        #kuhaon ang info provided by user
        firstname = request.form.get("firstname").capitalize()
        middlename = request.form.get("middlename").capitalize()
        lastname = request.form.get("lastname").capitalize()
        
        if len(firstname) > 12 or len(middlename) > 1 or len(lastname) > 12:
            name = db.execute("SELECT name FROM users WHERE id = ?", session["user_id"])[0]["name"]
            return render_template("update_fullname.html", name=name)

        #check if naa bay sulod ang firstname ug lastname (required)
        if not firstname or not lastname:
            session["error_fullname_sc"] = False
            session["error_fullname_required"] = True
            session["success_fullname"] = False
            name = db.execute("SELECT name FROM users WHERE id = ?", session["user_id"])[0]["name"]
            return render_template("update_fullname.html", name=name)

        #uban pa nga validation, balik ra ko ni like string length

        #check if tanan kay alpha
        if not firstname.isalpha() or not middlename.isalpha() or not lastname.isalpha():
            session["error_fullname_sc"] = True
            session["error_fullname_required"] = False
            session["success_fullname"] = False
            name = db.execute("SELECT name FROM users WHERE id = ?", session["user_id"])[0]["name"]
            return render_template("update_fullname.html", name=name)
        
        #if all goods
        db.execute("UPDATE users SET name = ? WHERE id = ?", firstname + ' ' + middlename + ' ' + lastname, session["user_id"])
        session["error_fullname_sc"] = False
        session["error_fullname_required"] = False
        session["success_fullname"] = True
        name = db.execute("SELECT name FROM users WHERE id = ?", session["user_id"])[0]["name"]
        return render_template("update_fullname.html", name=name)
        
        
            
    #if GET
    session["error_fullname_sc"] = False
    session["error_fullname_required"] = False
    session["success_fullname"] = False
    name = db.execute("SELECT name FROM users WHERE id = ?", session["user_id"])[0]["name"]
    return render_template("update_fullname.html", name=name)


@app.route("/add_pet", methods=["GET", "POST"])
@login_required
def add_pet():
    """Show history of transactions"""
    #if POST
    if request.method == "POST":
        #try if makaadd jud
        petname = request.form.get("petname")
        species = request.form.get("species")
        if len(petname) > 9 or len(species) > 9:
            print('lapas ang characters')
            return render_template('addpet.html')
        if not petname or not species:
            print('dili pde blangko ang name ug species')
            return render_template('addpet.html')
        file = request.files['file']
        filename = request.files['file'].filename
        #check if ang type sa file kay jpg or jpeg
        _, file_extension = os.path.splitext(filename)
        file_extension.lower()
        if file_extension not in ['.jpg', '.jpeg']:
            session["error_fileupload"] = True
            return render_template('addpet.html')

        #insert sa database ang infos
        finalfilename = session['username']+str(uuid.uuid4())+'.jpg'
        trysa = db.execute("SELECT petprofilefilename FROM pets WHERE user_id = ?", session['user_id'])
        print(f"{trysa}")
        db.execute("INSERT INTO pets (user_id, name, species, petprofilefilename) VALUES(?, ?, ?, ?)", session["user_id"], petname, species, finalfilename)
        filepath = os.path.join('static/images_pets/', finalfilename) #get the path
        file.save(filepath)

        #increment user's pet count
        current_numpets = db.execute("SELECT num_of_pets FROM users WHERE id = ?", session["user_id"])[0]['num_of_pets']
        db.execute("UPDATE users SET num_of_pets = ? + ? WHERE id = ?", current_numpets, 1, session['user_id'])
        #insert into activities table
        get_profile_pic = db.execute("SELECT profilepicfilename FROM users WHERE id = ?", session['user_id'])[0]['profilepicfilename']
        get_username = db.execute("SELECT username FROM users WHERE id = ?", session['user_id'])[0]['username']
        get_name = db.execute("SELECT name FROM users WHERE id = ?", session['user_id'])[0]['name']
        get_petname = petname
        db.execute("INSERT INTO activities (user_id, user_id_pic, user_id_username, user_id_name, user_id_petname, action) VALUES(?, ?, ?, ?, ?, ?)", session['user_id'], get_profile_pic, get_username, get_name, get_petname, "addpet")
        

    #IF GET
    return render_template("addpet.html")

@app.route("/remove_pet", methods=["POST"])
@login_required
def remove_pet():
    pet_id = request.form.get("pet_info")
    try:
        pet_id = int(pet_id)
    except:
        return render_template('error.html')
    
    #gather all the users pet(owner)
    pets = db.execute('SELECT id FROM pets WHERE user_id = ?', session['user_id'])
    list_ids = []
    for id in pets:
        list_ids.append(id['id'])

    if pet_id not in list_ids:
        return render_template('error.html')

    try:
        db.execute("DELETE FROM pets WHERE id = ? AND user_id = ?", pet_id, session['user_id'])
    except:
        print('something went wrong')
        return render_template('error.html')

    username = session['username']
    return redirect(f'/profile?n={username}')


@app.route("/top-up", methods=["GET", "POST"])
@login_required
def top_up():
    #if POST
    if request.method == "POST":
        token_amount = request.form.get("number_of_tokens")
        #update the database (love_tokens from users)
        get_current_lovetokens = db.execute("SELECT love_tokens FROM users WHERE id = ?", session["user_id"])[0]["love_tokens"]
        db.execute("UPDATE users SET love_tokens = ? + ? WHERE id = ?", get_current_lovetokens, token_amount, session["user_id"])
        print(f"{get_current_lovetokens}")

        return redirect("/top-up")
    #if GET
    return render_template("top-up.html")

@app.route("/follow", methods=["POST"])
@login_required
def follow():
    """Follow someone"""
    users_id = request.get_json()
    followers_count = db.execute('SELECT followers_count FROM users WHERE id = ?', users_id)[0]['followers_count']
    following_count = db.execute('SELECT following_count FROM users WHERE id = ?', session['user_id'])[0]['following_count']

    #check if you already followed the user
    check_followed = db.execute("SELECT * FROM followers WHERE user_id = ? AND followers_id = ?", users_id, session['user_id'])
    print(f'{check_followed}')
    if check_followed:
        #remove the record that the user followed that user
        db.execute("DELETE FROM followers WHERE user_id = ? AND followers_id = ?", users_id, session['user_id'])

        #decrement the users followers count
        db.execute("UPDATE users SET followers_count = ? - ? WHERE id = ?", followers_count, 1, users_id)
        #update the users following count
        db.execute("UPDATE users SET following_count = ? - ? WHERE id = ?", following_count, 1, session['user_id'])
        
        return jsonify({
            'followed': 'False',
        })
    else:
        #insert into followers table
        db.execute("INSERT INTO followers (user_id, followers_id) VALUES(?, ?)", users_id, session['user_id'])
        db.execute("UPDATE users SET followers_count = ? + ? WHERE id = ?", followers_count, 1, users_id)
        #insert into activities table
        get_profile_pic = db.execute("SELECT profilepicfilename FROM users WHERE id = ?", session['user_id'])[0]['profilepicfilename']
        get_username = db.execute("SELECT username FROM users WHERE id = ?", session['user_id'])[0]['username']
        get_name = db.execute("SELECT name FROM users WHERE id = ?", session['user_id'])[0]['name']
        get_target_username = db.execute("SELECT username FROM users WHERE id = ?", users_id)[0]['username']
        get_target_name = db.execute("SELECT name FROM users WHERE id = ?", users_id)[0]['name']
        db.execute("INSERT INTO activities (user_id, user_id_pic, user_id_username, user_id_name, target_id, target_id_username, target_id_name, action) VALUES(?, ?, ?, ?, ?, ?, ?, ?)", session['user_id'], get_profile_pic, get_username, get_name, users_id, get_target_username, get_target_name, "follow")
        #update the users following count
        db.execute("UPDATE users SET following_count = ? + ? WHERE id = ?", following_count, 1, session['user_id'])
        return jsonify({
            'followed': 'True',
        })



@app.route("/login", methods=["GET", "POST"])
def login():
    """Log user in"""

    if "user_id" in session:
        return redirect("/")

    session.clear()
    #if request method is post
    if request.method == "POST":
        #Ensure username was submitted
        if not request.form.get("username"):
            return redirect("/login")

        #Ensure password was submitted
        elif not request.form.get("password"):
            return redirect("/login")

        # Query database for username
        rows = db.execute(
            "SELECT * FROM users WHERE username = ?", request.form.get("username").lower()
        )

        # Ensure username exists and password is correct
        if len(rows) != 1 or not check_password_hash(
            rows[0]["hash"], request.form.get("password")
        ):
            session["wrong_credentials"] = True
            return render_template("login.html")

        # Remember which user has logged in
        session["user_id"] = rows[0]["id"]
        session["username"] = rows[0]["username"]
        

        # Redirect user to home page
        return redirect("/")

    # User reached route via GET (as by clicking a link or via redirect)
    else:
        return render_template("login.html")


@app.route("/logout")
def logout():
    """Log user out"""
    
    #clear session to logout
    session.clear()

    #redirect user to login form
    return redirect("/")



@app.route("/register", methods=["GET", "POST"])
def register():
    """Register user"""
    if request.method == "GET":
        return render_template("register.html")
    
    #if request method is POST
    username = request.form.get("username").lower()
    password = request.form.get("password")
    confirm = request.form.get("confirm")


    check_if_exist = db.execute('SELECT username FROM users WHERE username = ?', username)
    if check_if_exist:
        return redirect('/register')
    if not username or not password or not confirm:
        
        return redirect('/register')
    elif password != confirm:
        
        return redirect('/register')

    #kung all goods, insert dayun sa database
    try:
        db.execute(
            "INSERT INTO users (username, hash, name) VALUES(?, ?, ?)", username.lower(), generate_password_hash(password), username
        )
    except:
        print('the username is taken')
        return redirect('/register')

    #login the user
    session["user_id"] = db.execute("SELECT id FROM users WHERE username = ?", username)[0]["id"]
    session["username"] = username

    return redirect("/")
        
@app.route("/check_username_if_exist", methods=["POST"])
def check_username_if_exist():
    username = request.get_json()
    temp = db.execute("SELECT username FROM users WHERE username = ?", username)
    print(f'{username}')
    print(f'{temp}')
    if temp:
        return jsonify({
            'status': 'taken'
        })
    else:
        return jsonify({
            'status': 'available'
        })

@app.route("/error")
@login_required
def error():
    """Just a 'something went wrong'"""

    return render_template('error.html')

@app.route("/change_password", methods=["GET", "POST"])
@login_required
def change_password():
    #if POST
    if request.method == 'POST':
        old = request.form.get('oldpassword')
        new = request.form.get('newpassword')
        confirm = request.form.get('confirmnewpassword')

        if not old or not new or not confirm:
            return render_template('change-password.html')
    
        #get the users old password
        if not check_password_hash(db.execute('SELECT hash FROM users WHERE id = ?', session['user_id'])[0]['hash'], old):
            return render_template('change-password.html', color='red', feedback='Incorrect old password.')
        
        #check if the newpassword and confirm matches
        if new != confirm:
            return render_template('change-password.html', color='red', feedback="The new password does not match the confirmation. Please try again.")
        
        #if all goods 
        db.execute('UPDATE users SET hash = ? WHERE id = ?', generate_password_hash(new), session['user_id'])
        return render_template('change-password.html', color='green', feedback="Password changed successfully!")

    #if GET
    return render_template('change-password.html')


if __name__ == '__main__':
    app.run(debug=True)

