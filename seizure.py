import random
import numpy as np
from matplotlib import pyplot as plt
import matplotlib.pyplot as plt
from matplotlib import animation
from ftplib import FTP
import time
import mysql.connector
from twilio.rest import Client

count = 0
r = 1
j = 0
c = 0
# First set up the figure, the axis, and the plot element we want to animate
fig = plt.figure()
ax = plt.axes(xlim=(0, 6), ylim=(-2 ,2))
line, = ax.plot([], [], lw=2)
ax.set_xlabel('NORMAL HEART RATE')
# initialization function: plot the background of each frame
def init():
    line.set_data([], [])
    return line,
# animation function.  This is called sequentially
def animate(i):
    global count
    global r
    global j
    global c
    if(count > 100):
        r = random.randint(1,3+j)
        #print(r)
        if(r<3):
            ax.set_xlabel('NORMAL HEART RATE')
        elif(r>=3 and r<4):
            g = random.randint(1,4)
            if (g==1):
                ax.set_xlabel('JOGGING')

            elif (g==2):
                ax.set_xlabel('RUNNING')
            else:
                if (c==0):
                    c = 1
                ax.set_xlabel('MINOR SEIZURE')
        else:
            if (c==0):
                c = 1
            ax.set_xlabel('MAJOR SEIZURE')

        count = 0
        j+=1
        if (j>4):
            j=0
    x = np.linspace(0, 5, 100)
    y = np.sin(r * np.pi * (x + 0.01 * i))
    line.set_data(x, y)
    count+=1
    if (c==1):
        up = random.randint(0,4)
        ra = random.randint(0,100)
        fig.savefig('plot_'+str(ra)+'.png')
        c=3
        uploadpic(up, ra)
    return line,



def twilio(names,up, lat, lon):
    account_sid = 'AC8d1b43a2cd30c1487647a484d7b1c3c3'
    auth_token = 'fb721fcbecf089721dc7e075a7b3cbee'
    client = Client(account_sid, auth_token)

    message = client.messages \
                    .create(
                         body="EMERGENCY. I AM "+names[up]+" I AM GOING THROUGH A SEIZURE. MY LOCATION: {LATITUDE, LONGITUDE}"+str(lat)+", "+str(lon),
                         from_='+19165201589',
                         to='+19162610135'
                     )

    print(message.sid)

def data(up, filename):
    names = ['Jack' , 'Mike', 'Indi' , 'Chunu', 'Nic']
    lat = random.uniform(38.000000, 39.000000)
    lon = random.uniform(-122.000000, -121.000000)
    twilio(names, up, lat, lon)
    mydb = mysql.connector.connect(host="sql141.main-hosting.eu",user="u341003167_suser",passwd="mTen8VdtpkTvknSaxP",database="u341003167_seize")
    mycursor = mydb.cursor(buffered=True)
    sql = "INSERT INTO u341003167_seize.patients (name, link, latitude, longitude) VALUES (%s, %s, %s, %s)"
    data = (names[up],"/Uploads/"+filename, lat, lon)
    mycursor.execute(sql, data)
    mycursor.execute("SELECT * FROM patients")
    mydb.commit()
    
def uploadpic(up, ra):
    ftp = FTP('185.201.11.33')
    ftp.login(user='u341003167', passwd = 'FjEjvPEoS0priBaJos')
    ftp.cwd('Uploads/')
    filename = "plot_"+str(ra)+'.png'
    ftp.storbinary('STOR '+filename, open(filename, 'rb'))
    data(up,filename)
    ftp.quit()


# call the animator.  blit=True means only re-draw the parts that have changed.
anim = animation.FuncAnimation(fig, animate, init_func=init, frames=200, interval=20, blit=True)
plt.show()
