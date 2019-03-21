#!/bin/bash


# ============================================================================ #
# 				            docker-usermod.sh                                  #
# ============================================================================ #
#           Set docker container user id and user primary group id             #
#                      to match those of the host                              #
# ---------------------------------------------------------------------------- #


SCRIPT_NAME=docker-usermod

# script's exit code
SCRIPT_STAT=0;

# position of user id and group id fields in /etc/passwd
P_UID_F=3
P_PGID_F=4


function echo_colored()
{
	color=$1;
	text=$2;

	case "$color" in
	"red")	
		echo -e -n "\033[0;31m${text}\033[0m";
		;;
	"light_red")	
		echo -e -n "\033[1;31m${text}\033[0m";
		;;
	"green")
		echo -e -n "\033[0;32m${text}\033[0m";
		;;
	"light_green")
		echo -e -n "\033[1;32m${text}\033[0m";
		;;
	"yellow")
		echo -e -n "\033[0;33m${text}\033[0m";
		;;
	"blue")
		echo -e -n "\033[0;34m${text}\033[0m";
		;;
	esac
}


# source environment variables 
if [ ! -f '.env' ]; then
	echo -n "$SCRIPT_NAME: "
	echo_colored "yellow" "NOTE"
	echo ": '.env' file not found in script's directory.";
else
	set -a;
	. .env;
	set +a;
fi

# revoke env bash 'errexit' option (in order to show err messages before exiting this script). 
# It's restored at the end of this script.
set +e;


DOCKERUSER=localuser
DOCKERGROUP=localgroup
DOCKERCONTAINER=${COMPOSE_PROJECT_NAME}_${COMPOSE_PHP_MODULE}


# parse command line arguments if existant
# ----------------------------------------
if [ ! -z "$1" ] && [[ "$1" == "--help" || "$1" == "-h" ]]; then
	echo "USAGE:";
	echo -e "\t $SCRIPT_NAME: [DOCKERCONTAINER] [DOCKERUSER] [DOCKERGROUP]";
	exit 0;
fi

if [ ! -z "$1" ]; then
	DOCKERCONTAINER=$1;
fi
if [ ! -z "$2" ]; then
	DOCKERUSER=$2;
fi
if [ ! -z "$3" ]; then
	DOCKERGROUP=$3;
fi


# check if docker container is running
# ------------------------------------
sudo docker ps | grep $DOCKERCONTAINER > /dev/null

if [ $? -ne 0 ]; then
	echo "$SCRIPT_NAME: Docker container must be running before calling this script.";
	exit 1;
fi


# get host user id and primary group id
# -------------------------------------
if [ "$EUID" -eq 0 ]; then
	# important! 
	# if user started this script with 'sudo' get effective user data from it's exported variables
	USER="$SUDO_USER"
	USER_ID="$SUDO_UID"
	PRIM_GROUP_ID="$SUDO_GID"
else
	USER=`id -un`
	USER_ID=`id -u`
	PRIM_GROUP_ID=`id -g`
fi


# check if docker user can read /etc/passwd
# -----------------------------------------
docker_etc_ok=$(sudo docker exec $DOCKERCONTAINER bash -c 'if test -r /etc/passwd; then echo "OK"; else echo "FAIL"; fi;');
if [ $? -ne 0 ] || [ $docker_etc_ok == "FAIL" ]; then
	echo -n "$SCRIPT_NAME: "
	echo_colored "red" "Failed"
	echo ". Docker container user cannot read /etc/passwd."
	exit 1;
fi


# pass query commands to dockerd
# ------------------------------
dockeruser="$(sudo docker exec $DOCKERCONTAINER cat /etc/passwd | grep ^${DOCKERUSER}:)"
DOCKER_USER_ID=`echo $dockeruser | cut -f3 -d:`
DOCKER_PRIM_GROUP_ID=`echo $dockeruser | cut -f4 -d:`

if [ -z $DOCKER_USER_ID ]; then
	echo "$SCRIPT_NAME: Invalid docker user specified.";
	exit 1;
fi
if [ -z $DOCKER_PRIM_GROUP_ID ]; then
	echo "$SCRIPT_NAME: Invalid docker user primary group specified.";
	exit 1;
fi


# compare user id's and adapt docker's if not equal
# -------------------------------------------------
echo "$SCRIPT_NAME: host user id = $USER_ID   <->   docker container user id = $DOCKER_USER_ID";
if [ $USER_ID -ne $DOCKER_USER_ID ]; then
	echo -n "$SCRIPT_NAME: user ids' differ. Adapting docker user id...";
	sudo docker exec $DOCKERCONTAINER usermod -u $USER_ID $DOCKERUSER;

	if [ $? -eq 0 ]; then
		echo_colored "green" " done\n"
	else
		echo_colored "red"  " failed\n";
		SCRIPT_STAT=1;
	fi
else 
	echo -n "$SCRIPT_NAME: "
	echo_colored "green" "OK"
	echo ". Equal user ids'."
fi


# compare group id's and adapt docker's if not equal
# --------------------------------------------------
echo "$SCRIPT_NAME: host primary group id = $PRIM_GROUP_ID   <->   docker container user primary group id = $DOCKER_PRIM_GROUP_ID";
if [ $PRIM_GROUP_ID -ne $DOCKER_PRIM_GROUP_ID ]; then
	echo -n "$SCRIPT_NAME: user group primary ids' differ. Adapting docker user primary group id...";
	sudo docker exec $DOCKERCONTAINER groupmod -g $PRIM_GROUP_ID $DOCKERGROUP;

	if [ $? -eq 0 ]; then
		echo_colored "green" " done\n"
	else
		echo_colored "red"  " failed\n";
		SCRIPT_STAT=1;
	fi
else 
	echo -n "$SCRIPT_NAME: "
	echo_colored "green" "OK"
	echo ". Equal user primary group ids'."
fi


set -e;
exit $SCIRPT_STAT;





# alternative method to parse /etc/passwd
# ---------------------------------------
#while IFS=: read -r f1 f2 f3 f4 f5 f6 f7
#do
#	echo "$f1 $f3 $f4"
#done </etc/passwd


# alternative method to get user's groups
# ---------------------------------------
#if [ ! -r /etc/passwd ]; then
#	echo "$SCRIPT_NAME: Invalid permissions. Cannot read /etc/passwd.";
#	exit 1;
#fi
#USER=`cat /etc/passwd | grep ^$(whoami):`
#USER_ID=`echo $USER | cut -f3 -d:`
#PRIM_GROUP_ID=`echo $USER | cut -f4 -d:`
